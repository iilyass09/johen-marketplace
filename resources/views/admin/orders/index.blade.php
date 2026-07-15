@extends('admin.layouts.app')

@section('title', 'Manajemen Pesanan')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <h2 class="text-lg font-semibold">Semua Pesanan</h2>
    <span class="badge badge-neutral">{{ $orders->total() }} total</span>
</div>

<div class="search-wrap mb-4" style="max-width:320px">
    <i class="fas fa-search search-icon"></i>
    <input type="text" class="input-field" id="orderSearch" placeholder="Cari order ID, pelanggan..." style="padding-left:2.4rem">
</div>

<div class="table-wrap">
    <div class="overflow-x-auto">
        <table class="w-full" id="ordersTable">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Pelanggan</th>
                    <th>Produk</th>
                    <th>Penerima</th>
                    <th class="text-center">Status</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Tanggal</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td style="font-size:0.82rem;font-family:monospace">{{ $order->order_id }}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <div style="width:28px;height:28px;border-radius:7px;background:linear-gradient(135deg,var(--accent),#6366f1);display:flex;align-items:center;justify-content:center;font-size:0.6rem;font-weight:700;color:#fff;flex-shrink:0">
                                {{ substr($order->user->name ?? '?', 0, 1) }}
                            </div>
                            <span>{{ $order->user->name ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td style="font-size:0.85rem;color:var(--text-muted)">{{ $order->product_name }}</td>
                    <td style="font-size:0.82rem">{{ $order->customer_number }}</td>
                    <td class="text-center">
                        <span class="badge
                            @if($order->status === 'success') badge-success
                            @elseif($order->status === 'pending') badge-warning badge-pulse
                            @elseif($order->status === 'processing') badge-info
                            @else badge-error @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="text-right font-semibold" style="color:var(--accent);font-size:0.88rem">Rp {{ number_format($order->price, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-size:0.8rem;color:var(--text-muted)">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-primary btn-xs">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="fas fa-shopping-cart"></i>
                            <p>Belum ada pesanan</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrap">{{ $orders->links() }}</div>

@push('scripts')
<script>
document.getElementById('orderSearch')?.addEventListener('keyup', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#ordersTable tbody tr').forEach(row => {
        const txt = row.textContent.toLowerCase();
        row.style.display = txt.includes(q) ? '' : 'none';
    });
});
</script>
@endpush
@endsection