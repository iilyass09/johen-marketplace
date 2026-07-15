@extends('admin.layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
<div style="max-width:800px;margin:0 auto">
    <a href="{{ route('admin.orders') }}" class="btn btn-ghost mb-4" style="padding-left:0">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>

    <div class="card-glass p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-xl font-semibold">Detail Pesanan</h2>
                <p style="font-size:0.85rem;color:var(--text-muted);font-family:monospace;margin-top:0.25rem">{{ $order->order_id }}</p>
            </div>
            <span class="badge" style="font-size:0.85rem;padding:0.35rem 1rem;
                @if($order->status === 'success') background:rgba(16,185,129,0.12);color:var(--success)
                @elseif($order->status === 'pending') background:rgba(245,158,11,0.12);color:var(--warning)
                @elseif($order->status === 'processing') background:rgba(59,130,246,0.12);color:var(--info)
                @else background:rgba(239,68,68,0.12);color:var(--error) @endif">
                <i class="fas fa-circle" style="font-size:0.4rem;margin-right:0.35rem;vertical-align:middle"></i>
                {{ ucfirst($order->status) }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <p style="font-size:0.78rem;color:var(--text-dim);font-weight:600;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.25rem">Pelanggan</p>
                <p class="font-semibold">{{ $order->user->name ?? 'N/A' }}</p>
                <p style="font-size:0.85rem;color:var(--text-muted)">{{ $order->user->email ?? '' }}</p>
            </div>
            <div>
                <p style="font-size:0.78rem;color:var(--text-dim);font-weight:600;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.25rem">Tanggal</p>
                <p class="font-semibold">{{ $order->created_at->format('d M Y H:i') }}</p>
            </div>
            <div>
                <p style="font-size:0.78rem;color:var(--text-dim);font-weight:600;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.25rem">Produk</p>
                <p class="font-semibold">{{ $order->product_name }}</p>
                <p style="font-size:0.85rem;color:var(--text-muted)">{{ $order->brand }} - {{ $order->category }}</p>
            </div>
            <div>
                <p style="font-size:0.78rem;color:var(--text-dim);font-weight:600;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.25rem">Penerima</p>
                <p class="font-semibold">{{ $order->customer_number }}</p>
                <p style="font-size:0.85rem;color:var(--text-muted)">{{ $order->customer_name ?? 'Tanpa nickname' }}</p>
            </div>
        </div>

        <div style="border-top:1px solid var(--glass-border);padding-top:1rem;display:flex;justify-content:space-between;align-items:center">
            <p style="color:var(--text-muted)">Total Pembayaran</p>
            <p style="font-size:1.6rem;font-weight:800;background:linear-gradient(135deg,var(--accent),#6366f1);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">Rp {{ number_format($order->price, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="card-glass p-6 mb-6">
        <h3 class="font-semibold mb-4">
            <i class="fas fa-arrow-rotate" style="color:var(--accent);margin-right:0.5rem"></i>
            Update Status
        </h3>
        <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="flex items-center gap-4">
            @csrf
            @method('PATCH')
            <select name="status" class="input-field" style="max-width:200px">
                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="success" {{ $order->status === 'success' ? 'selected' : '' }}>Success</option>
                <option value="failed" {{ $order->status === 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>

    @if($order->transaction)
    <div class="card-glass p-6">
        <h3 class="font-semibold mb-4">
            <i class="fas fa-receipt" style="color:var(--accent);margin-right:0.5rem"></i>
            Info Transaksi
        </h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p style="font-size:0.78rem;color:var(--text-dim);font-weight:600;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.2rem">Transaction ID</p>
                <p style="font-size:0.85rem;font-family:monospace">{{ $order->transaction->transaction_id ?? '-' }}</p>
            </div>
            <div>
                <p style="font-size:0.78rem;color:var(--text-dim);font-weight:600;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.2rem">Tipe Pembayaran</p>
                <p style="font-size:0.85rem">{{ $order->transaction->payment_type ?? '-' }}</p>
            </div>
            <div>
                <p style="font-size:0.78rem;color:var(--text-dim);font-weight:600;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.2rem">Status</p>
                <p style="font-size:0.85rem">{{ $order->transaction->status ?? '-' }}</p>
            </div>
            <div>
                <p style="font-size:0.78rem;color:var(--text-dim);font-weight:600;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.2rem">Fraud Status</p>
                <p style="font-size:0.85rem">{{ $order->transaction->fraud_status ?? '-' }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection