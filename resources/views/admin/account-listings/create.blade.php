@extends('admin.layouts.app')

@section('title', 'Tambah Listing Akun')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('admin.account-listings') }}" class="text-purple-400 hover:text-purple-300 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>

    <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
        <h2 class="text-xl font-semibold mb-6">Tambah Listing Akun Baru</h2>

        <form action="{{ route('admin.account-listings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Game</label>
                    <input type="text" name="game" list="gameList" value="{{ old('game') }}" required
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500"
                           placeholder="Nama game (cth: Mobile Legends)">
                    <datalist id="gameList">
                        @foreach($brands as $brand)
                            <option value="{{ $brand->name }}">
                        @endforeach
                    </datalist>
                    @error('game') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Nama Produk</label>
                    <input type="text" name="product_name" value="{{ old('product_name') }}" required
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500"
                           placeholder="cth: Akun Mythic Glory 100+ Skins">
                    @error('product_name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Spesifikasi Produk</label>
                <textarea name="specifications" rows="5" required
                          class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500"
                          placeholder="Detail akun: rank, skin, emblem, dll">{{ old('specifications') }}</textarea>
                @error('specifications') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Harga Coret <span style="color:#ef4444;font-weight:400;font-size:.75rem">(original)</span></label>
                    <input type="number" name="original_price" value="{{ old('original_price') }}" step="0.01" min="0"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500"
                           placeholder="cth: 500000">
                    <p class="text-gray-500 text-xs mt-1">Harga sebelum diskon (dicoret)</p>
                    @error('original_price') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Harga Jual</label>
                    <input type="number" name="price" value="{{ old('price') }}" required step="0.01" min="0"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    @error('price') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Nama Owner</label>
                    <select name="owner_name"
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                        <option value="">Pilih Owner</option>
                        <option value="Johen PUBG" {{ old('owner_name') === 'Johen PUBG' ? 'selected' : '' }}>Johen PUBG</option>
                        <option value="Monkey PUBG" {{ old('owner_name') === 'Monkey PUBG' ? 'selected' : '' }}>Monkey PUBG</option>
                        <option value="Johen MLBB" {{ old('owner_name') === 'Johen MLBB' ? 'selected' : '' }}>Johen MLBB</option>
                        <option value="Johen FF" {{ old('owner_name') === 'Johen FF' ? 'selected' : '' }}>Johen FF</option>
                        <option value="Johen Roblox" {{ old('owner_name') === 'Johen Roblox' ? 'selected' : '' }}>Johen Roblox</option>
                        <option value="Johen E-Football" {{ old('owner_name') === 'Johen E-Football' ? 'selected' : '' }}>Johen E-Football</option>
                        <option value="Johen FCM" {{ old('owner_name') === 'Johen FCM' ? 'selected' : '' }}>Johen FCM</option>
                        <option value="Johen Valorant" {{ old('owner_name') === 'Johen Valorant' ? 'selected' : '' }}>Johen Valorant</option>
                    </select>
                    @error('owner_name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">No. WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp') }}"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500"
                           placeholder="6281234567890">
                    @error('whatsapp') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-6" style="border-top:1px solid var(--glass-border);padding-top:1.5rem">
                <h3 class="text-md font-semibold mb-4" style="color:var(--jba-accent, #9d5cf5)">Promo</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Status Promo</label>
                        <select name="promo_type" id="promo_type"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                            <option value="none" {{ old('promo_type') === 'none' ? 'selected' : '' }}>Tidak Ada</option>
                            <option value="promo" {{ old('promo_type') === 'promo' ? 'selected' : '' }}>Promo</option>
                            <option value="flash_sale" {{ old('promo_type') === 'flash_sale' ? 'selected' : '' }}>Flash Sale</option>
                            <option value="diskon" {{ old('promo_type') === 'diskon' ? 'selected' : '' }}>Diskon</option>
                            <option value="best_seller" {{ old('promo_type') === 'best_seller' ? 'selected' : '' }}>Best Seller</option>
                            <option value="hot" {{ old('promo_type') === 'hot' ? 'selected' : '' }}>Hot</option>
                            <option value="new" {{ old('promo_type') === 'new' ? 'selected' : '' }}>New</option>
                            <option value="limited" {{ old('promo_type') === 'limited' ? 'selected' : '' }}>Limited</option>
                        </select>
                        @error('promo_type') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-4" id="discount_field" style="{{ old('promo_type') === 'diskon' ? '' : 'display:none' }}">
                        <label class="block text-sm font-medium mb-2">Persentase Diskon (%)</label>
                        <input type="number" name="discount_percent" value="{{ old('discount_percent') }}" min="1" max="100"
                               class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500"
                               placeholder="cth: 25">
                        @error('discount_percent') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="mb-6" style="border-top:1px solid var(--glass-border);padding-top:1.5rem">
                <label class="flex items-center space-x-3">
                    <input type="checkbox" name="is_sold" value="1" {{ old('is_sold') ? 'checked' : '' }}
                           class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-purple-600 focus:ring-purple-500">
                    <span class="text-sm font-medium">Tandai sebagai <span style="color:#ef4444;font-weight:600">TERJUAL</span></span>
                </label>
                <p class="text-gray-500 text-xs mt-1 ml-1">Produk akan tetap ditampilkan tetapi dengan overlay gelap dan badge SOLD</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-2">Foto Produk</label>
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

            <div class="mb-6">
                <label class="block text-sm font-medium mb-3">Foto Detail Produk (maksimal 5)</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @for($i = 1; $i <= 5; $i++)
                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-400">Foto Detail {{ $i }}</label>
                        <input type="file" name="detail_photo_{{ $i }}" accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                        @error('detail_photo_'.$i) <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    @endfor
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.account-listings') }}" class="bg-gray-600 px-6 py-2 rounded-lg hover:bg-gray-500 transition">Batal</a>
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
document.getElementById('promo_type')?.addEventListener('change', function() {
    const field = document.getElementById('discount_field');
    field.style.display = this.value === 'diskon' ? '' : 'none';
    if (this.value !== 'diskon') {
        document.querySelector('[name="discount_percent"]').value = '';
    }
});
</script>
@endpush
