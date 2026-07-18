<?php

namespace App\Http\Controllers;

use App\Models\AccountListing;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminAccountListingController extends Controller
{
    public function index()
    {
        $listings = AccountListing::orderBy('game')->orderBy('product_name')->paginate(20);
        return view('admin.account-listings.index', compact('listings'));
    }

    public function create()
    {
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        return view('admin.account-listings.create', compact('brands'));
    }

    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'game' => 'required|string',
            'product_name' => 'required|string',
            'specifications' => 'required|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'owner_name' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'promo_type' => 'nullable|string|in:none,promo,flash_sale,diskon,best_seller,hot,new,limited',
            'discount_percent' => 'nullable|integer|min:1|max:100',
            'is_sold' => 'boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'detail_photo_1' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'detail_photo_2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'detail_photo_3' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'detail_photo_4' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'detail_photo_5' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'game' => $request->game,
            'product_name' => $request->product_name,
            'specifications' => $request->specifications,
            'price' => $request->price,
            'original_price' => $request->original_price,
            'owner_name' => $request->owner_name,
            'whatsapp' => $request->whatsapp,
            'promo_type' => $request->promo_type ?? 'none',
            'discount_percent' => $request->promo_type === 'diskon' ? $request->discount_percent : null,
            'is_sold' => $request->boolean('is_sold', false),
            'is_active' => true,
        ];

        foreach (['photo', 'detail_photo_1', 'detail_photo_2', 'detail_photo_3', 'detail_photo_4', 'detail_photo_5'] as $field) {
            if ($request->hasFile($field) && $request->file($field)->isValid()) {
                $data[$field] = $request->file($field)->store('account-listings', 'public');
            }
        }

        AccountListing::create($data);

        return redirect()->route('admin.account-listings')->with('success', 'Listing akun berhasil ditambahkan');
    }

    public function edit(AccountListing $accountListing)
    {
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        return view('admin.account-listings.edit', compact('accountListing', 'brands'));
    }

    public function update(Request $request, AccountListing $accountListing)
    {
        $validator = validator($request->all(), [
            'game' => 'required|string',
            'product_name' => 'required|string',
            'specifications' => 'required|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'owner_name' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'promo_type' => 'nullable|string|in:none,promo,flash_sale,diskon,best_seller,hot,new,limited',
            'discount_percent' => 'nullable|integer|min:1|max:100',
            'is_sold' => 'boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'detail_photo_1' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'detail_photo_2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'detail_photo_3' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'detail_photo_4' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'detail_photo_5' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'game' => $request->game,
            'product_name' => $request->product_name,
            'specifications' => $request->specifications,
            'price' => $request->price,
            'original_price' => $request->original_price,
            'owner_name' => $request->owner_name,
            'whatsapp' => $request->whatsapp,
            'promo_type' => $request->promo_type ?? 'none',
            'discount_percent' => $request->promo_type === 'diskon' ? $request->discount_percent : null,
            'is_sold' => $request->boolean('is_sold', false),
            'is_active' => $request->boolean('is_active', true),
        ];

        foreach (['photo', 'detail_photo_1', 'detail_photo_2', 'detail_photo_3', 'detail_photo_4', 'detail_photo_5'] as $field) {
            if ($request->hasFile($field) && $request->file($field)->isValid()) {
                if ($accountListing->$field) {
                    Storage::disk('public')->delete($accountListing->$field);
                }
                $data[$field] = $request->file($field)->store('account-listings', 'public');
            }
        }

        $accountListing->update($data);

        return redirect()->route('admin.account-listings')->with('success', 'Listing akun berhasil diperbarui');
    }

    public function toggle(AccountListing $accountListing)
    {
        $accountListing->update(['is_active' => !$accountListing->is_active]);
        return back()->with('success', 'Status listing akun berhasil diubah');
    }

    public function destroy(AccountListing $accountListing)
    {
        foreach (['photo', 'detail_photo_1', 'detail_photo_2', 'detail_photo_3', 'detail_photo_4', 'detail_photo_5'] as $field) {
            if ($accountListing->$field) {
                Storage::disk('public')->delete($accountListing->$field);
            }
        }
        $accountListing->delete();
        return redirect()->route('admin.account-listings')->with('success', 'Listing akun berhasil dihapus');
    }
}
