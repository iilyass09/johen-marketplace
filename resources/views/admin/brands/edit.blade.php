@extends('admin.layouts.app')
@section('title', 'Edit Game')
@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('admin.brands') }}" class="text-purple-400 hover:text-purple-300 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
    <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
        <h2 class="text-xl font-semibold mb-6">Edit Game: {{ $brand->name }}</h2>
        <form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Nama Game</label>
                    <input type="text" name="name" value="{{ old('name', $brand->name) }}" required
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    @error('name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Kategori</label>
                    <select name="category" required
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                        <option value="">Pilih kategori</option>
                        <option value="moba" {{ old('category', $brand->category) === 'moba' ? 'selected' : '' }}>MOBA</option>
                        <option value="fps" {{ old('category', $brand->category) === 'fps' ? 'selected' : '' }}>FPS</option>
                        <option value="br" {{ old('category', $brand->category) === 'br' ? 'selected' : '' }}>Battle Royale</option>
                        <option value="rpg" {{ old('category', $brand->category) === 'rpg' ? 'selected' : '' }}>RPG</option>
                        <option value="strategi" {{ old('category', $brand->category) === 'strategi' ? 'selected' : '' }}>Strategi</option>
                        <option value="racing" {{ old('category', $brand->category) === 'racing' ? 'selected' : '' }}>Racing</option>
                        <option value="sandbox" {{ old('category', $brand->category) === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                        <option value="other" {{ old('category', $brand->category) === 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('category') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Urutan</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $brand->sort_order) }}" min="0"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    <p class="text-gray-500 text-xs mt-1">Semakin kecil, semakin depan.</p>
                    @error('sort_order') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Thumbnail (JPG / PNG)</label>
                <div class="flex items-center gap-4">
                    <div class="w-24 h-24 rounded-lg bg-gray-700 border border-gray-600 flex items-center justify-center overflow-hidden flex-shrink-0">
                        @if($brand->thumbnail_url)
                            <img src="{{ $brand->thumbnail_url }}" alt="{{ $brand->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-gray-500 text-sm">No Image</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" name="thumbnail" id="thumbnail-input" accept="image/jpeg,image/png,image/jpg"
                               class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                        <p class="text-gray-500 text-xs mt-1">Kosongkan jika tidak ingin mengubah. Maksimal 2MB. Format: JPG atau PNG.</p>
                        @error('thumbnail') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Background Carousel (JPG / PNG) <span class="text-gray-500 text-xs">(opsional)</span></label>
                <div class="flex items-center gap-4">
                    <div class="w-24 h-24 rounded-lg bg-gray-700 border border-gray-600 flex items-center justify-center overflow-hidden flex-shrink-0">
                        @if($brand->carousel_bg_url)
                            <img src="{{ $brand->carousel_bg_url }}" alt="bg" class="w-full h-full object-cover">
                        @else
                            <span class="text-gray-500 text-sm">No Image</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" name="carousel_bg" id="carousel-bg-input" accept="image/jpeg,image/png,image/jpg"
                               class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                        <p class="text-gray-500 text-xs mt-1">Gambar latar di carousel halaman utama. Kosongkan jika tidak ingin mengubah.</p>
                        @error('carousel_bg') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">{{ old('description', $brand->description) }}</textarea>
                @error('description') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-6 mb-4">
                <label class="flex items-center space-x-3">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $brand->is_active) ? 'checked' : '' }}
                           class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-purple-600 focus:ring-purple-500">
                    <span class="text-sm font-medium">Aktif</span>
                </label>
                <label class="flex items-center space-x-3">
                    <input type="checkbox" name="is_popular" value="1" {{ old('is_popular', $brand->is_popular) ? 'checked' : '' }}
                           class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-purple-600 focus:ring-purple-500">
                    <span class="text-sm font-medium">Populer <span class="text-gray-500 text-xs">(tampil di carousel)</span></span>
                </label>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('admin.brands') }}" class="bg-gray-600 px-6 py-2 rounded-lg hover:bg-gray-500 transition">Batal</a>
                <button type="submit" class="bg-purple-600 px-6 py-2 rounded-lg hover:bg-purple-700 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
