@extends('layouts.topup')

@section('content')
<div class="orders-page">
    <div class="orders-header">
        <div>
            <h1 class="orders-title">Pesanan Saya</h1>
            <p class="orders-subtitle">{{ $orders->count() }} total pesanan</p>
        </div>
        @if($orders->isNotEmpty())
            <a href="{{ route('home') }}" class="btn btn-outline btn-sm">Top Up Lagi</a>
        @endif
    </div>

    @if($orders->isEmpty())
        <div class="orders-empty">
            <div class="orders-empty-icon">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
            </div>
            <h3 class="orders-empty-title">Belum Ada Pesanan</h3>
            <p class="orders-empty-desc">Kamu belum melakukan top up. Ayo top up sekarang!</p>
            <a href="{{ route('home') }}" class="btn btn-primary">Jelajahi Game</a>
        </div>
    @else
        <div class="orders-grid">
            @foreach($orders as $order)
                @php
                    $brand = $brands->get($order->brand);
                    $product = $products->get($order->buyer_sku_code);
                    $thumbnail = $brand?->thumbnail_url;
                    $productPhoto = $product?->photo_url;
                @endphp
                <div class="order-card">
                    <div class="order-card-top">
                        <div class="order-card-header">
                            <div class="order-game-icon" @if($thumbnail) style="background-image:url('{{ $thumbnail }}')" @endif>
                                @if(!$thumbnail)
                                    <span>{{ strtoupper(substr($order->brand, 0, 2)) }}</span>
                                @endif
                            </div>
                            <div class="order-game-meta">
                                <span class="order-game-name">{{ $order->brand }}</span>
                                <span class="order-date">{{ $order->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                        <span class="order-status order-status--{{ $order->status }}">
                            @if($order->status === 'success') Berhasil
                            @elseif($order->status === 'pending') Menunggu
                            @elseif($order->status === 'processing') Diproses
                            @else Gagal
                            @endif
                        </span>
                    </div>

                    <div class="order-card-body">
                        <div class="order-product-info">
                            @if($productPhoto)
                                <img src="{{ $productPhoto }}" alt="" class="order-product-photo">
                            @endif
                            <span class="order-product-name">{{ $order->product_name }}</span>
                        </div>
                    </div>

                    <div class="order-card-footer">
                        <div class="order-meta">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 9H2"/></svg>
                            {{ $order->customer_number }}
                        </div>
                        <div class="order-id">{{ $order->order_id }}</div>
                        <div class="order-price">Rp{{ number_format($order->price, 0, ',', '.') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
