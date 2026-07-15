@extends('admin.layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('admin.orders') }}" class="text-purple-400 hover:text-purple-300 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>

    <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-xl font-semibold">Detail Pesanan</h2>
                <p class="text-sm text-gray-400 font-mono mt-1">{{ $order->order_id }}</p>
            </div>
            <span class="px-4 py-2 rounded-full text-sm font-semibold
                @if($order->status === 'success') bg-green-600
                @elseif($order->status === 'pending') bg-yellow-600
                @elseif($order->status === 'processing') bg-blue-600
                @else bg-red-600 @endif">
                {{ ucfirst($order->status) }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-gray-400 text-sm">Pelanggan</p>
                <p class="font-semibold">{{ $order->user->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-400">{{ $order->user->email ?? '' }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Tanggal</p>
                <p class="font-semibold">{{ $order->created_at->format('d M Y H:i') }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Produk</p>
                <p class="font-semibold">{{ $order->product_name }}</p>
                <p class="text-sm text-gray-400">{{ $order->brand }} - {{ $order->category }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Penerima</p>
                <p class="font-semibold">{{ $order->customer_number }}</p>
                <p class="text-sm text-gray-400">{{ $order->customer_name ?? 'Tanpa nickname' }}</p>
            </div>
        </div>

        <div class="border-t border-gray-700 pt-4 flex justify-between items-center">
            <p class="text-gray-400">Total Pembayaran</p>
            <p class="text-2xl font-bold text-purple-400">Rp {{ number_format($order->price, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 mb-6">
        <h3 class="font-semibold mb-4">Update Status</h3>
        <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="flex items-center space-x-4">
            @csrf
            @method('PATCH')
            <select name="status" class="bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="success" {{ $order->status === 'success' ? 'selected' : '' }}>Success</option>
                <option value="failed" {{ $order->status === 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit" class="bg-purple-600 px-6 py-2 rounded-lg hover:bg-purple-700 transition">Update</button>
        </form>
    </div>

    @if($order->transaction)
    <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
        <h3 class="font-semibold mb-4">Info Transaksi</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-gray-400 text-sm">Transaction ID</p>
                <p class="text-sm font-mono">{{ $order->transaction->transaction_id ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Tipe Pembayaran</p>
                <p class="text-sm">{{ $order->transaction->payment_type ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Status</p>
                <p class="text-sm">{{ $order->transaction->status ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Fraud Status</p>
                <p class="text-sm">{{ $order->transaction->fraud_status ?? '-' }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
