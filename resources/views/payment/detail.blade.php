@extends('layouts.topup')

@section('title', 'Detail Pembayaran — ' . $order->order_id)

@section('content')
<div class="pd-wrap">
  <div class="pd-header">
    <div class="pd-header-badge">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
    </div>
    <div>
      <h1 class="pd-title">Detail Pembayaran</h1>
      <p class="pd-sub">Selesaikan pembayaran untuk melanjutkan pesanan</p>
    </div>
  </div>

  @if($isDemo)
  <div class="pd-demo-banner">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><circle cx="12" cy="12" r="4"/></svg>
    <span>Mode Demo — Midtrans belum dikonfigurasi. Tampilan hanya untuk preview.</span>
  </div>
  @endif

  <div class="pd-grid">
    <div class="pd-left">
      {{-- STATUS PEMBAYARAN --}}
      <div class="pd-card" id="pdStatusCard">
        <div class="pd-card-head">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          <span>Status Pembayaran</span>
        </div>
        <div class="pd-status" id="pdStatusBody">
          <div class="pd-status-left">
            <div class="pd-status-icon" id="pdStatusIcon">
              <svg class="spin-slow" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" width="44" height="44">
                <path d="M21 12a9 9 0 11-6.219-8.56"/>
              </svg>
            </div>
            <div class="pd-status-info">
              <div class="pd-status-label" id="pdStatusLabel">Menunggu Pembayaran</div>
              <div class="pd-status-sub" id="pdStatusSub">Selesaikan pembayaran sebelum batas waktu habis</div>
            </div>
          </div>
          <div class="pd-status-right">
            <span class="pd-status-pill" id="pdStatusPill">PENDING</span>
          </div>
        </div>
        <div class="pd-countdown" id="pdCountdown">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--text-mute)" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          <span>Sisa waktu:</span>
          <div class="pd-countdown-items">
            <span class="pd-cd-num" id="countHours">00</span>
            <span class="pd-cd-lbl">Jam</span>
            <span class="pd-cd-sep">:</span>
            <span class="pd-cd-num" id="countMinutes">00</span>
            <span class="pd-cd-lbl">Menit</span>
            <span class="pd-cd-sep">:</span>
            <span class="pd-cd-num" id="countSeconds">00</span>
            <span class="pd-cd-lbl">Detik</span>
          </div>
        </div>
      </div>

      {{-- METODE PEMBAYARAN --}}
      <div class="pd-card">
        <div class="pd-card-head">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
          <span>Metode Pembayaran</span>
        </div>
        <div class="pd-method" id="pdMethodBody">
          <div class="pd-method-placeholder" id="pdMethodPlaceholder">
            <div class="pd-method-illus">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--text-mute)" stroke-width="1.2">
                <rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M7 15h4"/>
              </svg>
            </div>
            <p class="pd-method-placeholder-text">Pilih metode pembayaran untuk melanjutkan</p>
            <button class="btn btn-solid btn-lg" id="payNowBtn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
              Bayar Sekarang
            </button>
          </div>
          <div class="pd-method-result" id="pdMethodResult" style="display:none">
            <div class="pd-method-result-head">
              <div class="pd-method-result-icon" id="pdMethodIcon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--purple-light)" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
              </div>
              <div>
                <div class="pd-method-result-label">Pembayaran via</div>
                <div class="pd-method-result-name" id="pdMethodName">-</div>
              </div>
            </div>

            <div class="pd-method-qr-wrap" id="pdMethodQrWrap" style="display:none">
              <div class="pd-method-qr" id="pdMethodQr"></div>
              <p class="pd-method-hint">Scan QR code menggunakan aplikasi e-wallet atau mobile banking</p>
            </div>

            <div class="pd-method-va-wrap" id="pdMethodVa" style="display:none">
              <div class="pd-va-box">
                <div class="pd-va-label">Nomor Virtual Account</div>
                <div class="pd-va-number" id="pdVaNumber">-</div>
                <button class="pd-copy-btn" data-copy="" id="pdVaCopyBtn">Salin Nomor VA</button>
              </div>
              <p class="pd-method-hint">Bayar melalui mobile banking / ATM / Internet Banking</p>
            </div>

            <div class="pd-method-conv" id="pdMethodConv" style="display:none">
              <p class="pd-method-hint" id="pdConvHint">Bayar di gerai terdekat</p>
            </div>

            <div class="pd-method-trans-id">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--text-mute)" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
              <span class="pd-trans-id-label">No. Transaksi</span>
              <span class="pd-trans-id-value">{{ $order->order_id }}</span>
              <button class="pd-copy-btn" data-copy="{{ $order->order_id }}">Salin</button>
            </div>
          </div>
          <div class="pd-method-success" id="pdMethodSuccess" style="display:none">
            <div class="pd-success-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="3" width="56" height="56"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
            <div class="pd-success-label">Pembayaran Berhasil</div>
            <div class="pd-success-sub">Pesanan sedang diproses</div>
          </div>
          <div class="pd-method-failed" id="pdMethodFailed" style="display:none">
            <div class="pd-failed-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2.5" width="56" height="56"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div class="pd-failed-label">Pembayaran Gagal</div>
            <div class="pd-failed-sub">Silakan coba lagi atau hubungi admin</div>
          </div>
        </div>
      </div>

    </div>

    <div class="pd-right">
      {{-- RINGKASAN PEMESANAN --}}
      <div class="pd-card pd-card-sticky">
        <div class="pd-card-head">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
          <span>Ringkasan Pemesanan</span>
        </div>
        <div class="pd-ringkasan">
          <div class="pd-ringkasan-product">
            <div class="pd-ringkasan-icon">
              @if($brand && $brand->thumbnail_url)
                <img src="{{ $brand->thumbnail_url }}" alt="{{ $order->brand }}" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">
              @else
                <svg width="24" height="24" viewBox="0 0 32 32" fill="none"><defs><linearGradient id="rg" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#9d5cf5"/><stop offset="100%" stop-color="#4c1d95"/></linearGradient></defs><polygon points="16,2 27,11 22,30 10,30 5,11" fill="url(#rg)"/></svg>
              @endif
            </div>
            <div>
              <div class="pd-ringkasan-game">{{ $order->brand }}</div>
              <div class="pd-ringkasan-pkg">{{ $order->product_name }} @if($order->quantity > 1)({{ $order->quantity }}x)@endif</div>
            </div>
          </div>
          <div class="pd-ringkasan-details">
            <div class="pd-ringkasan-row">
              <span class="pd-r-label">Akun</span>
              <span class="pd-r-value">{{ $order->customer_number }}@if($order->customer_name) ({{ $order->customer_name }})@endif</span>
            </div>
            <div class="pd-ringkasan-row">
              <span class="pd-r-label">ID Pesanan</span>
              <span class="pd-r-value pd-r-mono">{{ $order->order_id }}</span>
            </div>
            <div class="pd-ringkasan-row">
              <span class="pd-r-label">Status</span>
              <span class="pd-status-dot" id="pdRingkasanStatus">
                <span class="pd-dot pending"></span>
                <span id="pdRingkasanStatusText">Pending</span>
              </span>
            </div>
            <div class="pd-ringkasan-row">
              <span class="pd-r-label">Tanggal</span>
              <span class="pd-r-value">{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
          </div>
          <div class="pd-rincian" style="border-top:1px solid var(--border);padding:.8rem 1.1rem;">
            <div class="pd-rincian-row">
              <span>Harga Satuan</span>
              <span>Rp {{ number_format($order->price / max($order->quantity, 1), 0, ',', '.') }}</span>
            </div>
            <div class="pd-rincian-row">
              <span>Jumlah Pembelian</span>
              <span>{{ $order->quantity }}x</span>
            </div>
            <div class="pd-rincian-row">
              <span>Biaya Layanan</span>
              <span class="pd-green">Gratis</span>
            </div>
            <div class="pd-rincian-total">
              <span>Total Pembayaran</span>
              <span class="pd-total-price">Rp {{ number_format($order->price, 0, ',', '.') }}</span>
            </div>
          </div>
        </div>
        <div class="pd-ringkasan-actions">
          <a href="{{ route('orders.my') }}" class="btn btn-outline btn-full">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Lihat Semua Pesanan
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
@if(!$isDemo)
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endif
<script>
(function(){
'use strict';

const isDemo = @json($isDemo);
const orderId = @json($order->order_id);
const snapToken = @json($snapToken);
const statusUrl = @json(route('payment.status', $order));

/* ===== midtrans snap ===== */
const payBtn = document.getElementById('payNowBtn');

function openSnap() {
    if (!snapToken || isDemo) {
        simulatePayment();
        return;
    }
    snap.pay(snapToken, {
        onSuccess: function(result) {
            updateStatus('success', result);
        },
        onPending: function(result) {
            updateStatus('pending', result);
        },
        onError: function(result) {
            updateStatus('failed', result);
        },
        onClose: function() {
            const label = document.getElementById('pdStatusLabel');
            if (label.textContent === 'Pembayaran Berhasil') return;
        }
    });
}

function simulatePayment() {
    if (!isDemo || !payBtn) return;
    payBtn.disabled = true;
    payBtn.innerHTML = '<svg class="spin-slow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg> Memproses...';

    setTimeout(() => {
        document.getElementById('pdMethodPlaceholder').style.display = 'none';
        document.getElementById('pdMethodResult').style.display = 'block';
        document.getElementById('pdMethodQrWrap').style.display = 'block';
        document.getElementById('pdMethodQr').innerHTML =
            '<div class="pd-qr-dummy"><img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $order->order_id }}" alt="QR Code" style="width:180px;height:180px;border-radius:8px;display:block;background:#fff;"></div>';
        document.getElementById('pdMethodName').textContent = 'QRIS — Demo Mode';
        document.querySelector('#pdVaCopyBtn').dataset.copy = orderId;
        document.getElementById('pdStatusIcon').innerHTML =
            '<svg viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" width="44" height="44"><path d="M12 6v6l4 2"/></svg>';
        document.getElementById('pdStatusLabel').textContent = 'Menunggu Pembayaran';
        document.getElementById('pdStatusSub').textContent = 'Scan QR untuk menyelesaikan pembayaran (Demo)';
        document.getElementById('pdStatusPill').textContent = 'MENUNGGU';
        payBtn.disabled = false;
        payBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg> Bayar Sekarang';
    }, 1500);
}

function updateStatus(type, result) {
    const statusBody = document.getElementById('pdStatusBody');
    const icon = document.getElementById('pdStatusIcon');
    const label = document.getElementById('pdStatusLabel');
    const sub = document.getElementById('pdStatusSub');
    const pill = document.getElementById('pdStatusPill');
    const ringkasanStatus = document.getElementById('pdRingkasanStatusText');
    const ringkasanDot = document.querySelector('#pdRingkasanStatus .pd-dot');
    const countdown = document.getElementById('pdCountdown');
    const placeholder = document.getElementById('pdMethodPlaceholder');
    const methodResult = document.getElementById('pdMethodResult');
    const methodSuccess = document.getElementById('pdMethodSuccess');
    const methodFailed = document.getElementById('pdMethodFailed');

    if (type === 'success') {
        icon.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="2.5" width="44" height="44"><path d="M20 6 9 17l-5-5"/></svg>';
        label.textContent = 'Pembayaran Berhasil';
        sub.textContent = 'Pesanan sedang diproses secara otomatis';
        pill.textContent = 'BERHASIL';
        pill.style.background = 'rgba(52,211,153,.15)';
        pill.style.color = '#34d399';
        ringkasanStatus.textContent = 'Processing';
        ringkasanDot.className = 'pd-dot processing';
        countdown.style.display = 'none';
        placeholder.style.display = 'none';
        methodResult.style.display = 'none';
        methodSuccess.style.display = 'flex';
        methodFailed.style.display = 'none';
    } else if (type === 'failed') {
        icon.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2" width="44" height="44"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
        label.textContent = 'Pembayaran Gagal';
        sub.textContent = 'Silakan coba lagi atau hubungi admin';
        pill.textContent = 'GAGAL';
        pill.style.background = 'rgba(248,113,113,.15)';
        pill.style.color = '#f87171';
        ringkasanStatus.textContent = 'Failed';
        ringkasanDot.className = 'pd-dot failed';
        countdown.style.display = 'none';
        placeholder.style.display = 'none';
        methodResult.style.display = 'none';
        methodSuccess.style.display = 'none';
        methodFailed.style.display = 'flex';
    }
}

if (payBtn) {
    payBtn.addEventListener('click', openSnap);
}

if (!isDemo && snapToken) {
    setTimeout(openSnap, 600);
} else if (isDemo) {
    setTimeout(simulatePayment, 800);
}

/* ===== countdown ===== */
(function() {
    const created = new Date(@json($order->created_at->toIso8601String()));
    const expires = new Date(created.getTime() + 24 * 60 * 60 * 1000);

    function tick() {
        const diff = Math.max(0, expires - new Date());
        const h = Math.floor(diff / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);
        document.getElementById('countHours').textContent = String(h).padStart(2, '0');
        document.getElementById('countMinutes').textContent = String(m).padStart(2, '0');
        document.getElementById('countSeconds').textContent = String(s).padStart(2, '0');

        if (diff <= 0) {
            updateStatus('failed');
        }
    }
    tick();
    setInterval(tick, 1000);
})();

/* ===== polling status (only if not demo) ===== */
if (!isDemo) {
    (function poll() {
        let attempts = 0;
        function check() {
            fetch(statusUrl)
                .then(r => r.json())
                .then(data => {
                    const s = data.status;
                    if (s === 'success' || s === 'processing') {
                        updateStatus('success');
                        return;
                    }
                    if (s === 'failed') {
                        updateStatus('failed');
                        return;
                    }
                    attempts++;
                    if (attempts < 300) setTimeout(check, 3000);
                })
                .catch(() => { if (attempts < 300) setTimeout(check, 5000); });
        }
        setTimeout(check, 5000);
    })();
}

/* ===== copy button ===== */
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.pd-copy-btn');
    if (!btn) return;
    const text = btn.dataset.copy;
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.textContent;
        btn.textContent = 'Tersalin!';
        btn.style.background = 'rgba(52,211,153,.2)';
        btn.style.color = '#34d399';
        setTimeout(() => {
            btn.textContent = orig;
            btn.style.background = '';
            btn.style.color = '';
        }, 2000);
    });
});

})();
</script>
@endpush
