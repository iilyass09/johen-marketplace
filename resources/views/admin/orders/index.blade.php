@extends('admin.layouts.app')

@section('title', 'Manajemen Pesanan')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-semibold">Semua Pesanan</h2>
    <span class="bg-gray-700 px-3 py-1 rounded-full text-sm">{{ $orders->total() }} total</span>
</div>

<div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-gray-400 text-sm border-b border-gray-700">
                    <th class="text-left px-4 py-3">Order ID</th>
                    <th class="text-left px-4 py-3">Pelanggan</th>
                    <th class="text-left px-4 py-3">Produk</th>
                    <th class="text-left px-4 py-3">Penerima</th>
                    <th class="text-center px-4 py-3">Status</th>
                    <th class="text-right px-4 py-3">Total</th>
                    <th class="text-right px-4 py-3">Tanggal</th>
                    <th class="text-center px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr class="border-b border-gray-700 hover:bg-gray-700/50">
                    <td class="px-4 py-3 text-sm font-mono">{{ $order->order_id }}</td>
                    <td class="px-4 py-3">{{ $order->user->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-sm">{{ $order->product_name }}</td>
                    <td class="px-4 py-3 text-sm">{{ $order->customer_number }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            @if($order->status === 'success') bg-green-600
                            @elseif($order->status === 'pending') bg-yellow-600
                            @elseif($order->status === 'processing') bg-blue-600
                            @else bg-red-600 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($order->price, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right text-sm text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.orders.show', $order) }}" class="bg-purple-600 px-3 py-1.5 rounded text-xs hover:bg-purple-700 transition inline-flex items-center space-x-1">
                            <i class="fas fa-eye"></i>
                            <span>Detail</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-400">Belum ada pesanan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $orders->links() }}
</div>
@endsection
