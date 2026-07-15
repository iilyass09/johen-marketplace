@extends('admin.layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('admin.products') }}" class="text-purple-400 hover:text-purple-300 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>

    <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
        <h2 class="text-xl font-semibold mb-6">Tambah Produk Baru</h2>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Buyer SKU Code</label>
                    <input type="text" name="buyer_sku_code" value="{{ old('buyer_sku_code') }}" required
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    @error('buyer_sku_code') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Brand</label>
                    <input type="text" name="brand" value="{{ old('brand') }}" required
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    @error('brand') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Kategori</label>
                    <input type="text" name="category" value="{{ old('category') }}" required
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    @error('category') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Type</label>
                    <input type="text" name="type" value="{{ old('type') }}" required
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    @error('type') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Region</label>
                    <select name="region"
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                        <option value="">Semua Region</option>
                        <option value="ID" {{ old('region') === 'ID' ? 'selected' : '' }}>Indonesia (ID)</option>
                        <option value="MY" {{ old('region') === 'MY' ? 'selected' : '' }}>Malaysia (MY)</option>
                        <option value="PH" {{ old('region') === 'PH' ? 'selected' : '' }}>Philippines (PH)</option>
                    </select>
                    @error('region') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Nama Produk</label>
                    <input type="text" name="product_name" value="{{ old('product_name') }}" required
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    @error('product_name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-2">Foto Produk (opsional)</label>
                <div class="flex items-center gap-4">
                    <div id="photo-preview-container" class="w-20 h-20 rounded-lg bg-gray-700 border border-gray-600 flex items-center justify-center text-gray-500 text-xs overflow-hidden flex-shrink-0">
                        <span id="photo-preview-placeholder">Preview</span>
                        <img id="photo-preview-image" class="w-full h-full object-cover hidden" src="" alt="preview">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="photo" id="photo-input" accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                        <p class="text-gray-500 text-xs mt-1">Maksimal 2MB. Format: JPG, PNG, WebP.</p>
                        @error('photo') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Stok</label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    @error('stock') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Harga Modal</label>
                    <input type="number" name="price" value="{{ old('price') }}" required step="0.01" min="0"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    @error('price') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Harga Jual</label>
                    <input type="number" name="selling_price" value="{{ old('selling_price') }}" step="0.01" min="0"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    <p class="text-gray-500 text-xs mt-1">Kosongkan untuk menggunakan harga modal</p>
                    @error('selling_price') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('admin.products') }}" class="bg-gray-600 px-6 py-2 rounded-lg hover:bg-gray-500 transition">Batal</a>
                <button type="submit" class="bg-purple-600 px-6 py-2 rounded-lg hover:bg-purple-700 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('photo-input')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            const img = document.getElementById('photo-preview-image');
            const placeholder = document.getElementById('photo-preview-placeholder');
            img.src = ev.target.result;
            img.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
