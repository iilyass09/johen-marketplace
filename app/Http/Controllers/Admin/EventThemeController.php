<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventTheme;
use App\Services\MediaStore;
use App\Services\ThemeResolverService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventThemeController extends Controller
{
    protected const UPLOAD_DIR = 'event-themes';

    public function index()
    {
        $themes = EventTheme::orderByDesc('is_default')->orderByDesc('priority')->orderByDesc('id')->get();
        $aktif = app(ThemeResolverService::class)->resolveActiveTheme();

        return view('admin.event-themes.index', [
            'themes' => $themes,
            'aktif' => $aktif,
        ]);
    }

    public function create()
    {
        return view('admin.event-themes.form', [
            'theme' => new EventTheme,
            'aktif' => app(ThemeResolverService::class)->resolveActiveTheme(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['decorative_images'] = $this->storeDecorativeImages($request);

        if (! empty($data['is_default'])) {
            EventTheme::where('is_default', true)->update(['is_default' => false]);
        } else {
            if (EventTheme::where('is_default', true)->count() === 0) {
                $data['is_default'] = true;
            }
        }

        $theme = EventTheme::create($data);

        return redirect()->route('admin.event-themes.index')
            ->with('success', 'Tema "'.$theme->name.'" berhasil dibuat.');
    }

    public function edit(EventTheme $theme)
    {
        return view('admin.event-themes.form', [
            'theme' => $theme,
            'aktif' => app(ThemeResolverService::class)->resolveActiveTheme(),
        ]);
    }

    public function update(Request $request, EventTheme $theme)
    {
        $data = $this->validated($request, $theme->id);

        if ($request->boolean('remove_logo') && $theme->logo_override) {
            MediaStore::delete($theme->logo_override);
            $data['logo_override'] = null;
        }
        if ($request->boolean('remove_banner') && $theme->banner_image) {
            MediaStore::delete($theme->banner_image);
            $data['banner_image'] = null;
        }

        // Susun ulang gambar dekoratif: pertahankan yang tidak dihapus + tambah baru (maks 3)
        $existing = is_array($theme->decorative_images) ? $theme->decorative_images : [];
        $remove = array_map('intval', (array) $request->input('remove_deco', []));
        $kept = [];
        foreach ($existing as $idx => $img) {
            if (! is_string($img)) {
                continue;
            }
            if (in_array($idx, $remove, true)) {
                MediaStore::delete($img);
            } else {
                $kept[] = $img;
            }
        }
        foreach (($request->file('decorative_images') ?? []) as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }
            if (count($kept) >= 3) {
                break;
            }
            $kept[] = $this->storeThemeImage($file);
        }
        $data['decorative_images'] = $kept;

        if (! empty($data['is_default'])) {
            EventTheme::where('is_default', true)->where('id', '!=', $theme->id)->update(['is_default' => false]);
        }

        $theme->update($data);

        return redirect()->route('admin.event-themes.index')
            ->with('success', 'Tema "'.$theme->name.'" berhasil diperbarui.');
    }

    public function setDefault(Request $request, EventTheme $theme)
    {
        EventTheme::where('is_default', true)->update(['is_default' => false]);
        $theme->update(['is_default' => true, 'is_active' => true]);

        return redirect()->route('admin.event-themes.index')
            ->with('success', 'Tema "'.$theme->name.'" kini menjadi tema default.');
    }

    public function destroy(Request $request, EventTheme $theme)
    {
        if ($theme->is_default) {
            return redirect()->back()->with('error', 'Tema default tidak boleh dihapus.');
        }

        $this->deleteFiles($theme);
        $theme->delete();

        return redirect()->route('admin.event-themes.index')
            ->with('success', 'Tema "'.$theme->name.'" berhasil dihapus.');
    }

    public function toggle(Request $request, EventTheme $theme)
    {
        if ($theme->is_default) {
            return redirect()->back()->with('error', 'Tema default tidak bisa dinonaktifkan.');
        }

        $theme->update(['is_active' => ! $theme->is_active]);

        return redirect()->route('admin.event-themes.index')
            ->with('success', 'Tema "'.$theme->name.'" kini '.($theme->is_active ? 'Aktif' : 'Nonaktif').' (manual).');
    }

    public function resetDefault(Request $request)
    {
        EventTheme::where('is_default', false)->update(['is_active' => false]);

        return redirect()->route('admin.event-themes.index')
            ->with('success', 'Semua penonaktifan manual dibatalkan. Tema kembali ke default.');
    }

    public function preview(EventTheme $theme)
    {
        $url = route('home', ['preview_theme' => $theme->slug]);

        return redirect()->away($url);
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:event_themes,slug,'.($ignoreId ?? 'NULL').',id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
            'priority' => ['nullable', 'integer', 'min:0'],
            'colors.primary' => ['required', 'string'],
            'colors.accent' => ['required', 'string'],
            'colors.bg' => ['nullable', 'string'],
            'colors.bg_soft' => ['nullable', 'string'],
            'colors.card_bg' => ['nullable', 'string'],
            'colors.text' => ['nullable', 'string'],
            'colors.text_on_primary' => ['nullable', 'string'],
            'logo_override' => ['nullable', 'image', 'max:2048'],
            'banner_image' => ['nullable', 'image', 'max:5120'],
            'decorative_images' => ['nullable', 'array', 'max:3'],
            'decorative_images.*' => ['image', 'max:2048'],
            'particle_effect' => ['nullable', 'string', 'in:none,confetti,lantern,petals,fireworks'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_banner' => ['nullable', 'boolean'],
        ];

        $data = $request->validate($rules);

        $data['slug'] = Str::slug($data['slug'] ?: $request->input('name'));
        $data['is_default'] = $request->boolean('is_default');
        $data['is_active'] = $request->boolean('is_active');
        $data['priority'] = (int) ($data['priority'] ?? 0);

        $def = ThemeResolverService::defaultColors();
        $data['colors'] = [
            'primary' => $data['colors']['primary'],
            'accent' => $data['colors']['accent'],
            'bg' => $data['colors']['bg'] ?? $def['bg'],
            'bg_soft' => $data['colors']['bg_soft'] ?? $def['bg_soft'],
            'card_bg' => $data['colors']['card_bg'] ?? $def['card_bg'],
            'text' => $data['colors']['text'] ?? $def['text'],
            'text_on_primary' => $data['colors']['text_on_primary'] ?? $def['text_on_primary'],
        ];

        $data['particle_effect'] = empty($data['particle_effect']) ? null : $data['particle_effect'];

        if ($request->hasFile('logo_override')) {
            $data['logo_override'] = $this->storeThemeImage($request->file('logo_override'));
        }
        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $this->storeThemeImage($request->file('banner_image'));
        }

        return $data;
    }

    protected function storeDecorativeImages(Request $request): array
    {
        $paths = [];
        foreach (($request->file('decorative_images') ?? []) as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }
            if (count($paths) >= 3) {
                break;
            }
            $paths[] = $this->storeThemeImage($file);
        }

        return $paths;
    }

    protected function storeThemeImage(UploadedFile $file): string
    {
        $path = $file->store(self::UPLOAD_DIR, 'public');
        MediaStore::import($path);

        return $path;
    }

    protected function deleteFiles(EventTheme $theme): void
    {
        foreach (['logo_override', 'banner_image'] as $field) {
            if ($theme->{$field}) {
                MediaStore::delete($theme->{$field});
            }
        }
        foreach ((array) ($theme->decorative_images ?: []) as $img) {
            if ($img) {
                MediaStore::delete($img);
            }
        }
    }
}