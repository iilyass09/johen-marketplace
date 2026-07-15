@extends('layouts.topup')

@section('content')
<div class="max-w-lg mx-auto text-center">
    <h1 class="text-2xl font-bold mb-4">Pembayaran</h1>

    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 mb-6">
        <p class="text-gray-400">Pesanan: {{ $order->order_id }}</p>
        <p class="text-xl font-bold mt-2">{{ $order->product_name }}</p>
        <p class="text-gray-400">{{ $order->customer_number }}</p>
        <p class="text-3xl font-bold text-purple-400 mt-4">Rp {{ number_format($order->price, 0, ',', '.') }}</p>

        @if($snapResult['token'])
            <button id="pay-button" class="mt-6 w-full bg-purple-600 py-3 rounded-xl font-semibold hover:bg-purple-700 transition">
                Bayar Sekarang
            </button>
        @elseif($snapResult['redirect_url'])
            <a href="{{ $snapResult['redirect_url'] }}" class="mt-6 inline-block w-full bg-purple-600 py-3 rounded-xl font-semibold hover:bg-purple-700 transition text-center">
                Bayar Sekarang
            </a>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($snapResult['token'])
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    document.getElementById('pay-button').onclick = function() {
        snap.pay('{{ $snapResult['token'] }}', {
            onSuccess: function(result) { window.location.href = '{{ route('payment.success', $order) }}'; },
            onPending: function(result) { alert('Pembayaran tertunda. Silakan selesaikan pembayaran.'); },
            onError: function(result) { alert('Pembayaran gagal!'); },
            onClose: function() { alert('Popup ditutup. Silakan coba lagi.'); }
        });
    };
</script>
@endif
@endpush
