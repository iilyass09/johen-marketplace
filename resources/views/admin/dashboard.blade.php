@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">Total Produk</p>
                <p class="text-3xl font-bold mt-1">{{ $stats['total_products'] }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-600/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-box text-purple-400 text-xl"></i>
            </div>
        </div>
        <p class="text-green-400 text-sm mt-2">{{ $stats['active_products'] }} aktif</p>
    </div>

    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">Total Pesanan</p>
                <p class="text-3xl font-bold mt-1">{{ $stats['total_orders'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-600/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-shopping-cart text-blue-400 text-xl"></i>
            </div>
        </div>
        <p class="text-yellow-400 text-sm mt-2">{{ $stats['pending_orders'] }} pending</p>
    </div>

    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">Pendapatan</p>
                <p class="text-3xl font-bold mt-1 text-green-400">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-green-600/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-green-400 text-xl"></i>
            </div>
        </div>
        <p class="text-green-400 text-sm mt-2">{{ $stats['success_orders'] }} transaksi sukses</p>
    </div>

    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">Total Pengguna</p>
                <p class="text-3xl font-bold mt-1">{{ $stats['total_users'] }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-600/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-yellow-400 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-gray-800 rounded-xl border border-gray-700">
    <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center">
        <h2 class="text-lg font-semibold">Pesanan Terbaru</h2>
        <a href="{{ route('admin.orders') }}" class="text-purple-400 hover:text-purple-300 text-sm">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-gray-400 text-sm border-b border-gray-700">
                    <th class="text-left px-6 py-3">Order ID</th>
                    <th class="text-left px-6 py-3">Pelanggan</th>
                    <th class="text-left px-6 py-3">Produk</th>
                    <th class="text-left px-6 py-3">Status</th>
                    <th class="text-right px-6 py-3">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                <tr class="border-b border-gray-700 hover:bg-gray-700/50">
                    <td class="px-6 py-4 text-sm">{{ $order->order_id }}</td>
                    <td class="px-6 py-4">{{ $order->user->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-300">{{ $order->product_name }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            @if($order->status === 'success') bg-green-600
                            @elseif($order->status === 'pending') bg-yellow-600
                            @elseif($order->status === 'processing') bg-blue-600
                            @else bg-red-600 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right font-semibold">Rp {{ number_format($order->price, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada pesanan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
