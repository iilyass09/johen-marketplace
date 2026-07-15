@extends('admin.layouts.app')

@section('title', 'Edit Produk')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('admin.products') }}" class="text-purple-400 hover:text-purple-300 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>

    <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
        <h2 class="text-xl font-semibold mb-6">Edit Produk</h2>

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Buyer SKU Code</label>
                    <input type="text" name="buyer_sku_code" value="{{ old('buyer_sku_code', $product->buyer_sku_code) }}" required
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    @error('buyer_sku_code') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Brand</label>
                    <input type="text" name="brand" value="{{ old('brand', $product->brand) }}" required
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    @error('brand') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Kategori</label>
                    <input type="text" name="category" value="{{ old('category', $product->category) }}" required
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    @error('category') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Tipe Produk</label>
                    <select name="type" required
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                        <option value="instant" {{ old('type', $product->type) === 'instant' ? 'selected' : '' }}>Top Up (Instant)</option>
                        <option value="joki" {{ old('type', $product->type) === 'joki' ? 'selected' : '' }}>Joki</option>
                    </select>
                    @error('type') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Region</label>
                    <select name="region"
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                        <option value="">Semua Region</option>
                        <option value="ID" {{ old('region', $product->region) === 'ID' ? 'selected' : '' }}>Indonesia (ID)</option>
                        <option value="MY" {{ old('region', $product->region) === 'MY' ? 'selected' : '' }}>Malaysia (MY)</option>
                        <option value="PH" {{ old('region', $product->region) === 'PH' ? 'selected' : '' }}>Philippines (PH)</option>
                    </select>
                    @error('region') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Nama Produk</label>
                    <input type="text" name="product_name" value="{{ old('product_name', $product->product_name) }}" required
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    @error('product_name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Stok</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    @error('stock') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Harga Modal</label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" required step="0.01" min="0"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    @error('price') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Harga Jual</label>
                    <input type="number" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required step="0.01" min="0"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    @error('selling_price') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-2">Foto Produk (opsional)</label>
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded-lg bg-gray-700 border border-gray-600 flex items-center justify-center overflow-hidden flex-shrink-0">
                        @if($product->photo_url)
                            <img src="{{ $product->photo_url }}" alt="{{ $product->product_name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-gray-500 text-xs">No Image</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" name="photo" id="photo-input" accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                        <p class="text-gray-500 text-xs mt-1">Kosongkan jika tidak ingin mengubah. Maksimal 2MB. Format: JPG, PNG, WebP.</p>
                        @error('photo') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="flex items-center space-x-3">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                           class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-purple-600 focus:ring-purple-500">
                    <span class="text-sm">Produk Aktif</span>
                </label>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.products') }}" class="bg-gray-600 px-6 py-2 rounded-lg hover:bg-gray-500 transition">Batal</a>
                <button type="submit" class="bg-purple-600 px-6 py-2 rounded-lg hover:bg-purple-700 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
