<?php

namespace App\Http\Controllers;

use App\Models\FlashDeal;
use App\Models\Product;
use App\Services\ImageOptimizer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminFlashDealController extends Controller
{
    public function index(Request $request)
    {
        $query = FlashDeal::with('product');

        if ($request->filled('status')) {
            $now = now();
            if ($request->status === 'running') {
                $query->where('is_active', true)->where('starts_at', '<=', $now)->where('ends_at', '>=', $now);
            } elseif ($request->status === 'scheduled') {
                $query->where('is_active', true)->where('starts_at', '>', $now);
            } elseif ($request->status === 'ended') {
                $query->where(function ($q) use ($now) {
                    $q->where('ends_at', '<', $now)->orWhere('is_active', false);
                });
            }
        }

        if ($request->filled('brand')) {
            $query->whereHas('product', fn ($q) => $q->where('brand', $request->brand));
        }

        $flashDeals = $query->latest()->paginate(20);
        $products = Product::where('is_active', true)
            ->orderBy('brand')
            ->orderBy('product_name')
            ->get(['id', 'brand', 'product_name', 'selling_price']);
        $brands = $products->pluck('brand')->unique()->sort()->values();

        return view('admin.flash-deals.index', compact('flashDeals', 'products', 'brands'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if ($overlap = $this->findOverlap($data['product_id'], $data['starts_at'], $data['ends_at'])) {
            return $this->fail($request, 'Produk sudah memiliki flash deal aktif pada periode yang sama.');
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $data['image'] = ImageOptimizer::storeOptimized($request->file('image'), 'flash-deals', 640, 640);
        }

        FlashDeal::create($data);

        return $this->ok($request, 'Flash deal berhasil ditambahkan');
    }

    public function update(Request $request, FlashDeal $flashDeal)
    {
        $data = $this->validateData($request);

        if ($overlap = $this->findOverlap($data['product_id'], $data['starts_at'], $data['ends_at'], $flashDeal->id)) {
            return $this->fail($request, 'Produk sudah memiliki flash deal aktif pada periode yang sama.');
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($flashDeal->image) {
                Storage::disk('public')->delete($flashDeal->image);
            }
            $data['image'] = ImageOptimizer::storeOptimized($request->file('image'), 'flash-deals', 640, 640);
        } elseif ($request->boolean('remove_image') && $flashDeal->image) {
            Storage::disk('public')->delete($flashDeal->image);
            $data['image'] = null;
        }

        $flashDeal->update($data);

        return $this->ok($request, 'Flash deal berhasil diperbarui');
    }

    public function toggle(FlashDeal $flashDeal)
    {
        $flashDeal->update(['is_active' => ! $flashDeal->is_active]);

        return back()->with('success', 'Status flash deal berhasil diubah');
    }

    public function destroy(FlashDeal $flashDeal)
    {
        if ($flashDeal->image) {
            Storage::disk('public')->delete($flashDeal->image);
        }

        $flashDeal->delete();

        return redirect()->route('admin.flash-deals')->with('success', 'Flash deal berhasil dihapus');
    }

    private function validateData(Request $request): array
    {
        $validator = validator($request->all(), [
            'product_id' => 'required|exists:products,id',
            'discount_percent' => 'required|numeric|min:1|max:100',
            'stock' => 'required|integer|min:1',
            'starts_at' => 'required|date',
            'duration_hours' => 'nullable|numeric|min:0.1|max:720',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                abort(response()->json(['errors' => $validator->errors()->messages()], 422));
            }
            redirect()->back()->withErrors($validator)->withInput()->throwResponse();
        }

        $startsAt = Carbon::parse($request->starts_at);

        if ($request->filled('duration_hours') && (float) $request->duration_hours > 0) {
            $endsAt = $startsAt->copy()->addHours((float) $request->duration_hours);
        } else {
            $endsAt = Carbon::parse($request->ends_at);
        }

        if ($endsAt <= $startsAt) {
            if ($request->ajax() || $request->wantsJson()) {
                abort(response()->json(['errors' => ['ends_at' => ['Waktu selesai harus setelah waktu mulai.']]], 422));
            }
            redirect()->back()->withErrors(['ends_at' => 'Waktu selesai harus setelah waktu mulai.'])->withInput()->throwResponse();
        }

        return [
            'product_id' => $request->product_id,
            'discount_percent' => $request->discount_percent,
            'stock' => $request->stock,
            'starts_at' => $startsAt->toDateTimeString(),
            'ends_at' => $endsAt->toDateTimeString(),
            'is_active' => $request->boolean('is_active', true),
        ];
    }

    private function findOverlap(int $productId, $startsAt, $endsAt, ?int $ignoreId = null)
    {
        return FlashDeal::where('product_id', $productId)
            ->where('is_active', true)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt)
            ->first();
    }

    private function ok(Request $request, string $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('admin.flash-deals')->with('success', $message);
    }

    private function fail(Request $request, string $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['errors' => ['_flash' => [$message]]], 422);
        }

        return redirect()->back()->with('error', $message);
    }
}
