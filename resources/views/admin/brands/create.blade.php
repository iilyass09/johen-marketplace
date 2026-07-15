@extends('admin.layouts.app')
@section('title', 'Tambah Game')
@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('admin.brands') }}" class="text-purple-400 hover:text-purple-300 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
    <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
        <h2 class="text-xl font-semibold mb-6">Tambah Game Baru</h2>
        <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Nama Game</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Mobile Legends"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    @error('name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Kategori</label>
                    <select name="category" required
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                        <option value="">Pilih kategori</option>
                        <option value="moba" {{ old('category') === 'moba' ? 'selected' : '' }}>MOBA</option>
                        <option value="fps" {{ old('category') === 'fps' ? 'selected' : '' }}>FPS</option>
                        <option value="br" {{ old('category') === 'br' ? 'selected' : '' }}>Battle Royale</option>
                        <option value="rpg" {{ old('category') === 'rpg' ? 'selected' : '' }}>RPG</option>
                        <option value="strategi" {{ old('category') === 'strategi' ? 'selected' : '' }}>Strategi</option>
                        <option value="racing" {{ old('category') === 'racing' ? 'selected' : '' }}>Racing</option>
                        <option value="sandbox" {{ old('category') === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                        <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('category') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Urutan</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    <p class="text-gray-500 text-xs mt-1">Semakin kecil, semakin depan.</p>
                    @error('sort_order') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Thumbnail (JPG / PNG)</label>
                <div class="flex items-center gap-4">
                    <div id="preview-container" class="w-24 h-24 rounded-lg bg-gray-700 border border-gray-600 flex items-center justify-center text-gray-500 text-sm overflow-hidden" style="{{ old('_thumbnail_preview') ? '' : '' }}">
                        <span id="preview-placeholder">Preview</span>
                        <img id="preview-image" class="w-full h-full object-cover hidden" src="" alt="preview">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="thumbnail" id="thumbnail-input" accept="image/jpeg,image/png,image/jpg"
                               class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                        <p class="text-gray-500 text-xs mt-1">Maksimal 2MB. Format: JPG atau PNG.</p>
                        @error('thumbnail') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Background Carousel (JPG / PNG) <span class="text-gray-500 text-xs">(opsional)</span></label>
                <div class="flex items-center gap-4">
                    <div id="bg-preview-container" class="w-24 h-24 rounded-lg bg-gray-700 border border-gray-600 flex items-center justify-center text-gray-500 text-sm overflow-hidden">
                        <span id="bg-preview-placeholder">Preview</span>
                        <img id="bg-preview-image" class="w-full h-full object-cover hidden" src="" alt="preview">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="carousel_bg" id="carousel-bg-input" accept="image/jpeg,image/png,image/jpg"
                               class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                        <p class="text-gray-500 text-xs mt-1">Gambar latar di carousel halaman utama. Maksimal 2MB.</p>
                        @error('carousel_bg') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Deskripsi (opsional)</label>
                <textarea name="description" rows="3" placeholder="Deskripsi singkat tentang game ini"
                          class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-6 mb-4">
                <label class="flex items-center space-x-3">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-purple-600 focus:ring-purple-500">
                    <span class="text-sm font-medium">Aktif</span>
                </label>
                <label class="flex items-center space-x-3">
                    <input type="checkbox" name="is_popular" value="1" {{ old('is_popular') ? 'checked' : '' }}
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

@push('scripts')
<script>
document.getElementById('thumbnail-input')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            const img = document.getElementById('preview-image');
            const placeholder = document.getElementById('preview-placeholder');
            img.src = ev.target.result;
            img.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
});
document.getElementById('carousel-bg-input')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            const img = document.getElementById('bg-preview-image');
            const placeholder = document.getElementById('bg-preview-placeholder');
            img.src = ev.target.result;
            img.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
@endsection
