@extends('layouts.topup')

@section('content')
<div class="mb-6">
    <a href="{{ route('home') }}" class="text-purple-400 hover:text-purple-300">&larr; Kembali</a>
    <h1 class="text-2xl font-bold mt-2">Daftar Produk</h1>
</div>

@php $selectedBrand = request('brand'); @endphp

@if($selectedBrand)
    <div class="mb-4">
        <span class="bg-purple-600 px-3 py-1 rounded-full text-sm">{{ $selectedBrand }}</span>
        <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-white ml-2 text-sm">Hapus filter</a>
    </div>
@endif

@foreach($products as $brand => $items)
    @if($selectedBrand && $brand !== $selectedBrand)
        @continue
    @endif
    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">{{ $brand }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($items as $product)
            <div class="bg-gray-800 rounded-xl p-4 border border-gray-700">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-semibold">{{ $product->product_name }}</h3>
                        <span class="text-xs text-gray-500">{{ $product->category }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($product->stock < 1)
                            <span class="text-xs bg-red-600 px-2 py-1 rounded">Habis</span>
                        @elseif($product->stock < 10)
                            <span class="text-xs bg-yellow-600 px-2 py-1 rounded">Sisa {{ $product->stock }}</span>
                        @endif
                        <span class="text-xs bg-gray-700 px-2 py-1 rounded">{{ $product->type }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold text-purple-400">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                    @if($product->stock > 0)
                        <a href="{{ route('orders.create', $product) }}" class="mt-2 inline-block bg-purple-600 px-4 py-2 rounded-lg text-sm hover:bg-purple-700 transition">
                        Beli
                        </a>
                    @else
                        <span class="mt-2 inline-block bg-gray-700 px-4 py-2 rounded-lg text-sm text-gray-400 cursor-not-allowed">Stok Habis</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
@endforeach
@endsection
