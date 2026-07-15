@extends('layouts.topup')

@section('title', 'Dashboard - Johen Gaming')

@section('content')
<section style="max-width:1400px;margin:2rem auto 0;padding:0 2rem;">
  <div style="margin-bottom:2rem;">
    <h1 style="font-size:1.6rem;font-weight:800;">Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
    <p style="color:var(--text-dim);font-size:.88rem;margin-top:.3rem;">Kelola top up game dan lihat aktivitas kamu di sini.</p>
  </div>

  <div class="dash-grid">
    <div class="dash-card">
      <div class="dash-card-label">Total Pesanan</div>
      <div class="dash-card-value"><span class="purple">{{ $totalOrders }}</span></div>
    </div>
    <div class="dash-card">
      <div class="dash-card-label">Total Pengeluaran</div>
      <div class="dash-card-value"><span class="purple">Rp {{ number_format($totalSpent, 0, ',', '.') }}</span></div>
    </div>
    <div class="dash-card">
      <div class="dash-card-label">Member Sejak</div>
      <div class="dash-card-value" style="font-size:1rem;">{{ Auth::user()->created_at->format('d M Y') }}</div>
    </div>
  </div>

  <div style="display:flex;gap:.7rem;margin-bottom:2rem;flex-wrap:wrap;">
    <a href="{{ route('products.index') }}" class="btn btn-solid btn-lg">Top Up Sekarang <span>›</span></a>
    <a href="{{ route('orders.my') }}" class="btn btn-outline btn-lg">Pesanan Saya <span>›</span></a>
  </div>

  <div style="margin-top:2rem;">
    <h2 style="font-size:1.2rem;font-weight:700;margin-bottom:1rem;">Pesanan Terbaru</h2>
    @if($recentOrders->isEmpty())
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-md);padding:2rem;text-align:center;">
        <p style="font-size:3rem;margin-bottom:.5rem;">📦</p>
        <p style="color:var(--text-dim);font-size:.88rem;">Belum ada pesanan. Yuk top up sekarang!</p>
        <a href="{{ route('products.index') }}" class="btn btn-solid" style="margin-top:1rem;">Mulai Top Up</a>
      </div>
    @else
      @foreach($recentOrders as $order)
        <div class="dash-order-item">
          <div class="dash-order-info">
            <div class="dash-order-name">{{ $order->product_name }}</div>
            <div class="dash-order-meta">{{ $order->order_id }} &middot; {{ $order->created_at->format('d M Y H:i') }}</div>
          </div>
          <div style="text-align:right;">
            <div class="dash-order-price">Rp {{ number_format($order->price, 0, ',', '.') }}</div>
            <span style="display:inline-block;padding:.15rem .5rem;border-radius:5px;font-size:.66rem;font-weight:700;margin-top:.2rem;
              @if($order->status === 'success') background:rgba(52,211,153,.15);color:#34d399;
              @elseif($order->status === 'pending') background:rgba(251,191,36,.15);color:#fbbf24;
              @elseif($order->status === 'processing') background:rgba(96,165,250,.15);color:#60a5fa;
              @else background:rgba(248,113,113,.15);color:#f87171; @endif">
              {{ ucfirst($order->status) }}
            </span>
          </div>
        </div>
      @endforeach
      <a href="{{ route('orders.my') }}" style="display:inline-block;margin-top:.5rem;font-size:.82rem;color:var(--purple-light);font-weight:600;">
        Lihat Semua Pesanan →
      </a>
    @endif
  </div>
</section>
@endsection
