@extends('layouts.topup')

@section('content')
<h1 class="text-2xl font-bold mb-6">Pesanan Saya</h1>

@if($orders->isEmpty())
    <p class="text-gray-400">Belum ada pesanan.</p>
@else
    <div class="space-y-4">
        @foreach($orders as $order)
        <div class="bg-gray-800 rounded-xl p-4 border border-gray-700 flex justify-between items-center">
            <div>
                <p class="font-semibold">{{ $order->product_name }}</p>
                <p class="text-sm text-gray-400">{{ $order->customer_number }} &middot; {{ $order->order_id }}</p>
                <p class="text-sm text-gray-400">{{ $order->created_at->format('d M Y H:i') }}</p>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                    @if($order->status === 'success') bg-green-600
                    @elseif($order->status === 'pending') bg-yellow-600
                    @elseif($order->status === 'processing') bg-blue-600
                    @else bg-red-600 @endif">
                    {{ ucfirst($order->status) }}
                </span>
                <p class="text-purple-400 font-bold mt-1">Rp {{ number_format($order->price, 0, ',', '.') }}</p>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
