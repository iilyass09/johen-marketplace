@extends('layouts.topup')

@section('content')
<div class="max-w-lg mx-auto">
    <a href="{{ route('games.show', $product->brand) }}" class="text-purple-400 hover:text-purple-300">&larr; Kembali</a>
    <h1 class="text-2xl font-bold mt-2 mb-6">Beli {{ $product->product_name }}</h1>

    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <p class="text-sm text-gray-400">{{ $product->brand }}</p>
                <p class="font-semibold text-lg">{{ $product->product_name }}</p>
            </div>
            <p class="text-xl font-bold text-purple-400">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
        </div>
    </div>

    <form action="{{ route('orders.store') }}" method="POST" class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">

        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">ID Game / Nomor HP</label>
            <input type="text" name="customer_number" placeholder="Masukkan ID game atau nomor HP" required
                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
            @error('customer_number')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-2">Nickname (opsional)</label>
            <input type="text" name="customer_name" placeholder="Nickname game"
                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
        </div>

        <button type="submit" class="w-full bg-purple-600 py-3 rounded-xl font-semibold hover:bg-purple-700 transition">
            Lanjut ke Pembayaran
        </button>
    </form>
</div>
@endsection
