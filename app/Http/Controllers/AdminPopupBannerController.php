<?php

namespace App\Http\Controllers;

use App\Models\PopupBanner;
use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminPopupBannerController extends Controller
{
    public function index()
    {
        $popupBanners = PopupBanner::orderBy('sort_order')->orderBy('id')->paginate(20);
        return view('admin.popup-banners.index', compact('popupBanners'));
    }

    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'orientation' => 'required|string|in:portrait,landscape',
            'image_fit' => 'nullable|string|in:contain,cover',
            'image_position' => 'nullable|string|in:top,center,bottom',
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
            'description' => $request->description,
            'orientation' => $request->orientation,
            'image_fit' => $request->input('image_fit', 'contain'),
            'image_position' => $request->input('image_position', 'center'),
            'link' => $request->link,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->integer('sort_order', 0),
            'starts_at' => $request->filled('starts_at') ? $request->starts_at : null,
            'ends_at' => $request->filled('ends_at') ? $request->ends_at : null,
        ];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $data['image'] = $this->storeImage($request);
        }

        PopupBanner::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Popup banner berhasil ditambahkan']);
        }

        return redirect()->route('admin.popup-banners')->with('success', 'Popup banner berhasil ditambahkan');
    }

    public function update(Request $request, PopupBanner $popupBanner)
    {
        $validator = validator($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'orientation' => 'required|string|in:portrait,landscape',
            'image_fit' => 'nullable|string|in:contain,cover',
            'image_position' => 'nullable|string|in:top,center,bottom',
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
            'description' => $request->description,
            'orientation' => $request->orientation,
            'image_fit' => $request->input('image_fit', 'contain'),
            'image_position' => $request->input('image_position', 'center'),
            'link' => $request->link,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->integer('sort_order', 0),
            'starts_at' => $request->filled('starts_at') ? $request->starts_at : null,
            'ends_at' => $request->filled('ends_at') ? $request->ends_at : null,
        ];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($popupBanner->image) {
                Storage::disk('public')->delete($popupBanner->image);
            }
            $data['image'] = $this->storeImage($request);
        }

        $popupBanner->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Popup banner berhasil diperbarui']);
        }

        return redirect()->route('admin.popup-banners')->with('success', 'Popup banner berhasil diperbarui');
    }

    private function storeImage(Request $request): string
    {
        return ImageOptimizer::storeOptimized($request->file('image'), 'popup-banners', 1200, 1600);
    }

    public function toggle(PopupBanner $popupBanner)
    {
        $popupBanner->update(['is_active' => !$popupBanner->is_active]);
        return back()->with('success', 'Status popup banner berhasil diubah');
    }

    public function destroy(PopupBanner $popupBanner)
    {
        if ($popupBanner->image) {
            Storage::disk('public')->delete($popupBanner->image);
        }
        $popupBanner->delete();
        return redirect()->route('admin.popup-banners')->with('success', 'Popup banner berhasil dihapus');
    }
}