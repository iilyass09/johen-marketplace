@extends('admin.layouts.app')
@section('title', 'Edit Metode Pembayaran')
@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('admin.payment-methods') }}" class="text-purple-400 hover:text-purple-300 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
    <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
        <h2 class="text-xl font-semibold mb-6">Edit Metode Pembayaran</h2>
        <form action="{{ route('admin.payment-methods.update', $paymentMethod) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Nama Metode</label>
                    <input type="text" name="name" value="{{ old('name', $paymentMethod->name) }}" required
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    @error('name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Kode Unik</label>
                    <input type="text" name="code" value="{{ old('code', $paymentMethod->code) }}" required
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                    @error('code') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Foto (JPG / PNG / WebP)</label>
                    <div class="flex items-center gap-3">
                        <div class="w-16 h-16 rounded-lg bg-gray-700 border border-gray-600 flex items-center justify-center overflow-hidden flex-shrink-0">
                            @if($paymentMethod->photo_url)
                                <img src="{{ $paymentMethod->photo_url }}" alt="{{ $paymentMethod->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-gray-500 text-xs">{{ $paymentMethod->icon ?? '💳' }}</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" name="photo" id="photo-input" accept="image/jpeg,image/png,image/jpg,image/webp"
                                   class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                            <p class="text-gray-500 text-xs mt-1">Kosongkan jika tidak ingin mengubah. Maksimal 2MB.</p>
                            @error('photo') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="flex items-center space-x-3" style="padding-top:1.5rem;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $paymentMethod->is_active) ? 'checked' : '' }}
                               class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-purple-600 focus:ring-purple-500">
                        <span class="text-sm font-medium">Aktif</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('admin.payment-methods') }}" class="bg-gray-600 px-6 py-2 rounded-lg hover:bg-gray-500 transition">Batal</a>
                <button type="submit" class="bg-purple-600 px-6 py-2 rounded-lg hover:bg-purple-700 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
