@extends('admin.layouts.app')
@section('title', $theme->exists ? 'Edit Tema' : 'Buat Tema Baru')
@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('admin.event-themes') }}" class="inline-block mb-4" style="color:var(--accent)">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>

    @php
        $def = \App\Services\ThemeResolverService::defaultColors();
        $colors = is_array($theme->colors) ? array_merge($def, $theme->colors) : $def;
    @endphp

    <div class="card-glass p-6" style="border:1px solid var(--glass-border);border-radius:16px">
        <h2 class="text-xl font-bold mb-6">{{ $theme->exists ? 'Edit Tema: '.$theme->name : 'Buat Tema Baru' }}</h2>

        <form action="{{ $theme->exists ? route('admin.event-themes.update', $theme) : route('admin.event-themes.store') }}"
              method="POST" enctype="multipart/form-data">
            @csrf
            @if($theme->exists) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Nama Tema *</label>
                    <input type="text" name="name" id="f_name" value="{{ old('name', $theme->name) }}" required class="input-field" placeholder="Contoh: Ramadhan 2026">
                    @error('name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Slug *</label>
                    <input type="text" name="slug" id="f_slug" value="{{ old('slug', $theme->slug) }}" required class="input-field" placeholder="otomatis dari nama">
                    @error('slug') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Tanggal Mulai <span style="color:var(--text-dim);font-size:.72rem">(jadwal)</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', $theme->start_date?->format('Y-m-d')) }}" class="input-field">
                    @error('start_date') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Tanggal Selesai</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $theme->end_date?->format('Y-m-d')) }}" class="input-field">
                    @error('end_date') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Priority</label>
                    <input type="number" name="priority" value="{{ old('priority', $theme->priority ?? 0) }}" min="0" class="input-field">
                    @error('priority') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-6 mt-5">
                <label class="flex items-center gap-2" style="cursor:pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $theme->is_active) ? 'checked' : '' }}
                           style="width:16px;height:16px;accent-color:var(--accent);cursor:pointer">
                    <span class="text-sm font-medium">Aktifkan manual sekarang</span>
                </label>
                <label class="flex items-center gap-2" style="cursor:pointer">
                    <input type="checkbox" name="is_default" value="1" {{ old('is_default', $theme->is_default) ? 'checked' : '' }}
                           style="width:16px;height:16px;accent-color:var(--accent);cursor:pointer">
                    <span class="text-sm font-medium">Jadikan tema default</span>
                </label>
            </div>

            <div class="mt-6">
                <h3 class="text-sm font-bold mb-1" style="color:var(--accent)">Warna Tema</h3>
                <p style="color:var(--text-dim);font-size:.78rem;margin-bottom:1rem">Warna ini akan diterapkan ke seluruh tampilan website (tombol, header, background, teks).</p>
                @php
                    $colorFields = [
                        ['key' => 'primary', 'label' => 'Primary *', 'hint' => 'gradient & aksen utama'],
                        ['key' => 'accent', 'label' => 'Accent *', 'hint' => 'harga, rating, gold'],
                        ['key' => 'bg', 'label' => 'Background', 'hint' => 'latar halaman'],
                        ['key' => 'bg_soft', 'label' => 'Background Soft', 'hint' => 'latar section'],
                        ['key' => 'card_bg', 'label' => 'Kartu / Surface', 'hint' => 'background kartu'],
                        ['key' => 'text', 'label' => 'Warna Teks', 'hint' => 'teks utama'],
                        ['key' => 'text_on_primary', 'label' => 'Teks di Primary', 'hint' => 'teks pada tombol'],
                    ];
                @endphp
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($colorFields as $cf)
                        @php $cd = $colors[$cf['key']]; @endphp
                        <div>
                            <label class="block text-xs font-semibold mb-1">{{ $cf['label'] }}<br><span style="color:var(--text-dim);font-weight:400">{{ $cf['hint'] }}</span></label>
                            <div style="display:flex;gap:6px;align-items:center">
                                <input type="color" data-cp="color_text_{{ $cf['key'] }}" value="{{ old('colors.'.$cf['key'], $cd) }}"
                                       style="width:44px;height:38px;flex-shrink:0;border:none;border-radius:8px;cursor:pointer;background:var(--bg-input)">
                                <input type="text" name="colors[{{ $cf['key'] }}]" id="color_text_{{ $cf['key'] }}" value="{{ old('colors.'.$cf['key'], $cd) }}"
                                       class="input-field" style="font-family:monospace;font-size:.75rem;padding:.35rem .5rem">
                            </div>
                            @error('colors.'.$cf['key']) <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endforeach
                </div>
                <p style="color:var(--text-dim);font-size:.78rem;margin-top:1rem">Kosongkan field warna untuk memakai nilai bawaan theme.</p>
            </div>

            <div class="mt-6">
                <h3 class="text-sm font-bold mb-1" style="color:var(--accent)">Logo Tema</h3>
                <p style="color:var(--text-dim);font-size:.78rem;margin-bottom:.75rem">Logo khusus event (PNG transparan). Jika kosong, logo website dipakai.</p>
                <div class="flex items-center gap-4">
                    <div id="logoPreview" style="width:88px;height:56px;border-radius:12px;background:var(--bg-input);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
                        @if($theme->logo_override)
                            <img src="{{ asset('storage/'.$theme->logo_override) }}" style="width:100%;height:100%;object-fit:contain" alt="logo">
                        @else
                            <span style="font-size:.7rem;color:var(--text-dim)">Preview</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" name="logo_override" id="logoInput" accept="image/png,image/webp,image/jpeg" class="w-full text-sm" style="color:var(--text-muted)">
                        <div class="flex items-center gap-4 mt-1.5">
                            <p style="color:var(--text-dim);font-size:.72rem">Maks 2MB. PNG transparan disarankan.</p>
                            @if($theme->logo_override)
                                <label class="flex items-center gap-1.5" style="cursor:pointer;font-size:.78rem">
                                    <input type="checkbox" name="remove_logo" value="1" style="accent-color:var(--error)"> Hapus logo
                                </label>
                            @endif
                        </div>
                        @error('logo_override') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-sm font-bold mb-1" style="color:var(--accent)">Banner Hero</h3>
                <p style="color:var(--text-dim);font-size:.78rem;margin-bottom:.75rem">Banner hero utama yang menimpa slider default di halaman beranda.</p>
                <div class="flex items-center gap-4">
                    <div id="bannerPreview" style="width:140px;height:64px;border-radius:12px;background:var(--bg-input);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
                        @if($theme->banner_image)
                            <img src="{{ asset('storage/'.$theme->banner_image) }}" style="width:100%;height:100%;object-fit:cover" alt="banner">
                        @else
                            <span style="font-size:.7rem;color:var(--text-dim)">Preview</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" name="banner_image" id="bannerInput" accept="image/jpeg,image/png,image/webp" class="w-full text-sm" style="color:var(--text-muted)">
                        <div class="flex items-center gap-4 mt-1.5">
                            <p style="color:var(--text-dim);font-size:.72rem">Maks 5MB. Rasio rekomendasi 16:9 / 1920×1080.</p>
                            @if($theme->banner_image)
                                <label class="flex items-center gap-1.5" style="cursor:pointer;font-size:.78rem">
                                    <input type="checkbox" name="remove_banner" value="1" style="accent-color:var(--error)"> Hapus banner
                                </label>
                            @endif
                        </div>
                        @error('banner_image') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-sm font-bold mb-1" style="color:var(--accent)">Gambar Dekoratif Mengambang</h3>
                <p style="color:var(--text-dim);font-size:.78rem;margin-bottom:.75rem">Maks 3 gambar kecil (PNG transparan, ikon tema seperti bintang/kue/lampion) yang mengambang di latar halaman.</p>
                @if($theme->exists && !empty($theme->decorative_images))
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        @foreach($theme->decorative_images as $idx => $img)
                            @if(!is_string($img)) @continue @endif
                            <div class="card-glass p-3" style="border:1px solid var(--glass-border)">
                                <img src="{{ asset('storage/'.$img) }}" alt="Dekorasi {{ $idx+1 }}" style="width:100%;height:72px;object-fit:contain">
                                <label class="flex items-center gap-1.5 mt-2" style="cursor:pointer;font-size:.78rem">
                                    <input type="checkbox" name="remove_deco[]" value="{{ $idx }}" style="accent-color:var(--error)"> Hapus
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif
                <input type="file" name="decorative_images[]" id="decoInput" accept="image/png,image/webp,image/jpeg" multiple class="w-full text-sm" style="color:var(--text-muted)">
                <p style="color:var(--text-dim);font-size:.72rem;margin-top:.4rem">Pilih hingga 3 gambar. Gambar lama akan dipertahankan jika tidak dicentang hapus.</p>
                @error('decorative_images') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mt-6">
                <h3 class="text-sm font-bold mb-1" style="color:var(--accent)">Efek Partikel</h3>
                <p style="color:var(--text-dim);font-size:.78rem;margin-bottom:.75rem">Efek dekoratif di latar halaman untuk suasana event.</p>
                <select name="particle_effect" class="input-field" style="max-width:260px">
                    @foreach(['' => 'Tidak ada', 'confetti' => 'Konfeti', 'lantern' => 'Lampion', 'petals' => 'Bunga Berjatuhan', 'fireworks' => 'Kembang Api'] as $val => $label)
                        <option value="{{ $val }}" {{ old('particle_effect', $theme->particle_effect) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-3 mt-8">
                <a href="{{ route('admin.event-themes') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>{{ $theme->exists ? 'Simpan Perubahan' : 'Simpan Tema' }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('f_name')?.addEventListener('input', function () {
    const slug = this.value.trim().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    if (slug && !document.getElementById('f_slug').dataset.touched) {
        document.getElementById('f_slug').value = slug;
    }
});
document.getElementById('f_slug')?.addEventListener('input', function () { this.dataset.touched = '1'; });
function previewFile(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if (!input || !preview) return;
    input.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (ev) {
            preview.innerHTML = '<img src="' + ev.target.result + '" style="width:100%;height:100%;object-fit:contain" alt="preview">';
        };
        reader.readAsDataURL(file);
    });
}
previewFile('logoInput', 'logoPreview');
previewFile('bannerInput', 'bannerPreview');
document.querySelectorAll('input[type="color"][data-cp]').forEach(function (cp) {
    const txt = document.getElementById(cp.dataset.cp);
    if (!txt) return;
    cp.addEventListener('input', function () { txt.value = this.value; });
    txt.addEventListener('input', function () {
        if (/^#[0-9a-fA-F]{6}$/.test(this.value)) cp.value = this.value;
    });
});
</script>
@endpush
@endsection