<?php

namespace App\Http\Controllers;

use App\Models\FlashSaleBanner;
use App\Services\ImageOptimizer;
use App\Services\MediaStore;
use Illuminate\Http\Request;

class AdminFlashSaleBannerController extends Controller
{
    public function index()
    {
        $flashSaleBanners = FlashSaleBanner::orderBy('sort_order')->orderBy('id')->paginate(20);
        return view('admin.flash-sale-banners.index', compact('flashSaleBanners'));
    }

    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'link' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()->messages()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'title' => $request->title,
            'link' => $request->link,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->integer('sort_order', 0),
            'starts_at' => $request->filled('starts_at') ? $request->starts_at : null,
            'ends_at' => $request->filled('ends_at') ? $request->ends_at : null,
        ];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $data['image'] = $this->storeImage($request);
        }

        FlashSaleBanner::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Banner flash sale berhasil ditambahkan']);
        }

        return redirect()->route('admin.flash-sale-banners')->with('success', 'Banner flash sale berhasil ditambahkan');
    }

    public function update(Request $request, FlashSaleBanner $flashSaleBanner)
    {
        $validator = validator($request->all(), [
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'link' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()->messages()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'title' => $request->title,
            'link' => $request->link,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->integer('sort_order', 0),
            'starts_at' => $request->filled('starts_at') ? $request->starts_at : null,
            'ends_at' => $request->filled('ends_at') ? $request->ends_at : null,
        ];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($flashSaleBanner->image) {
                MediaStore::delete($flashSaleBanner->image);
            }
            $data['image'] = $this->storeImage($request);
        }

        $flashSaleBanner->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Banner flash sale berhasil diperbarui']);
        }

        return redirect()->route('admin.flash-sale-banners')->with('success', 'Banner flash sale berhasil diperbarui');
    }

    private function storeImage(Request $request): string
    {
        return ImageOptimizer::storeOptimized($request->file('image'), 'flash-sale-banners', 1600, 800);
    }

    public function toggle(FlashSaleBanner $flashSaleBanner)
    {
        $flashSaleBanner->update(['is_active' => !$flashSaleBanner->is_active]);
        return back()->with('success', 'Status banner flash sale berhasil diubah');
    }

    public function destroy(FlashSaleBanner $flashSaleBanner)
    {
        if ($flashSaleBanner->image) {
            MediaStore::delete($flashSaleBanner->image);
        }
        $flashSaleBanner->delete();
        return redirect()->route('admin.flash-sale-banners')->with('success', 'Banner flash sale berhasil dihapus');
    }
}