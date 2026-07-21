@extends('layouts.topup')

@section('title', 'Pembayaran - ' . $listing->product_name . ' - Johen Gaming')

@section('content')
<div class="jpay-wrap">
  <div class="jpay-header">
    <div class="jpay-header-badge">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
    </div>
    <div>
      <h1 class="jpay-title">Detail Pembayaran</h1>
      <p class="jpay-sub">Selesaikan pembayaran untuk melanjutkan pemesanan akun</p>
    </div>
  </div>

  <div class="jpay-demo-banner">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><circle cx="12" cy="12" r="4"/></svg>
    <span>Mode Demo — Halaman pembayaran hanya untuk preview.</span>
  </div>

  <div class="jpay-grid">
    <div class="jpay-left">

      <div class="jpay-card">
        <div class="jpay-card-head">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          <span>Status Pembayaran</span>
        </div>
        <div class="jpay-status" id="jpayStatusBody">
          <div class="jpay-status-left">
            <div class="jpay-status-icon">
              <svg class="spin-slow" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" width="44" height="44">
                <path d="M21 12a9 9 0 11-6.219-8.56"/>
              </svg>
            </div>
            <div class="jpay-status-info">
              <div class="jpay-status-label">Menunggu Pembayaran</div>
              <div class="jpay-status-sub">Selesaikan pembayaran sebelum batas waktu habis</div>
            </div>
          </div>
          <div class="jpay-status-right">
            <span class="jpay-status-pill" id="jpayStatusPill">PENDING</span>
          </div>
        </div>
        <div class="jpay-countdown" id="jpayCountdown">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--text-mute)" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          <span>Sisa waktu:</span>
          <div class="jpay-countdown-items">
            <span class="jpay-cd-num" id="countHours">00</span>
            <span class="jpay-cd-lbl">Jam</span>
            <span class="jpay-cd-sep">:</span>
            <span class="jpay-cd-num" id="countMinutes">00</span>
            <span class="jpay-cd-lbl">Menit</span>
            <span class="jpay-cd-sep">:</span>
            <span class="jpay-cd-num" id="countSeconds">00</span>
            <span class="jpay-cd-lbl">Detik</span>
          </div>
        </div>
      </div>

      <div class="jpay-card">
        <div class="jpay-card-head">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
          <span>Metode Pembayaran</span>
        </div>
        <div class="jpay-method" id="jpayMethodBody">
          <div class="jpay-method-placeholder" id="jpayMethodPlaceholder">
            <div class="jpay-method-illus">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--text-mute)" stroke-width="1.2">
                <rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M7 15h4"/>
              </svg>
            </div>
            <p class="jpay-method-placeholder-text">Klik tombol di bawah untuk melihat detail pembayaran</p>
            <button class="btn btn-solid btn-lg" id="jpayShowBtn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
              Lihat Pembayaran
            </button>
          </div>
          <div class="jpay-method-result" id="jpayMethodResult" style="display:none">
            <div class="jpay-method-result-head">
              <div class="jpay-method-result-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--purple-light)" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
              </div>
              <div>
                <div class="jpay-method-result-label">Pembayaran via</div>
                <div class="jpay-method-result-name">{{ $accountOrder->payment_method }}</div>
              </div>
            </div>

            <div class="jpay-method-qr-wrap" id="jpayMethodQrWrap">
              <div class="jpay-method-qr">
                <div class="jpay-qr-dummy">
                  <svg width="180" height="180" viewBox="0 0 180 180" fill="none">
                    <rect width="180" height="180" rx="8" fill="white"/>
                    <rect x="16" y="16" width="148" height="148" fill="white"/>
                    <rect x="22" y="22" width="36" height="36" fill="#1a1a2e"/>
                    <rect x="68" y="22" width="8" height="8" fill="#1a1a2e"/>
                    <rect x="84" y="22" width="8" height="8" fill="#1a1a2e"/>
                    <rect x="108" y="22" width="8" height="8" fill="#1a1a2e"/>
                    <rect x="124" y="22" width="36" height="36" fill="#1a1a2e"/>
                    <rect x="22" y="68" width="8" height="8" fill="#1a1a2e"/>
                    <rect x="38" y="68" width="8" height="8" fill="#1a1a2e"/>
                    <rect x="60" y="68" width="28" height="28" fill="#1a1a2e"/>
                    <rect x="100" y="68" width="8" height="8" fill="#1a1a2e"/>
                    <rect x="124" y="68" width="8" height="8" fill="#1a1a2e"/>
                    <rect x="140" y="68" width="20" height="8" fill="#1a1a2e"/>
                    <rect x="22" y="84" width="8" height="8" fill="#1a1a2e"/>
                    <rect x="60" y="84" width="8" height="8" fill="#1a1a2e"/>
                    <rect x="84" y="84" width="8" height="8" fill="#1a1a2e"/>
                    <rect x="100" y="84" width="36" height="8" fill="#1a1a2e"/>
                    <rect x="22" y="108" width="8" height="8" fill="#1a1a2e"/>
                    <rect x="38" y="108" width="20" height="8" fill="#1a1a2e"/>
                    <rect x="68" y="108" width="36" height="28" fill="#1a1a2e"/>
                    <rect x="124" y="108" width="8" height="8" fill="#1a1a2e"/>
                    <rect x="140" y="108" width="8" height="8" fill="#1a1a2e"/>
                    <rect x="22" y="124" width="36" height="36" fill="#1a1a2e"/>
                    <rect x="84" y="124" width="8" height="8" fill="#1a1a2e"/>
                    <rect x="108" y="124" width="8" height="8" fill="#1a1a2e"/>
                    <rect x="124" y="124" width="8" height="8" fill="#1a1a2e"/>
                    <rect x="140" y="124" width="20" height="20" fill="#1a1a2e"/>
                    <rect x="22" y="38" width="8" height="14" fill="#1a1a2e"/>
                    <rect x="140" y="22" width="20" height="14" fill="#1a1a2e"/>
                    <rect x="140" y="86" width="8" height="14" fill="#1a1a2e"/>
                    <rect x="108" y="38" width="8" height="14" fill="#1a1a2e"/>
                    <rect x="60" y="108" width="8" height="8" fill="white"/>
                    <rect x="100" y="108" width="8" height="8" fill="white"/>
                    <rect x="44" y="84" width="8" height="8" fill="white"/>
                  </svg>
                </div>
              </div>
              <p class="jpay-method-hint">Scan QR code menggunakan aplikasi e-wallet atau mobile banking</p>
            </div>

            <div class="jpay-method-va-wrap" style="display:none">
              <div class="jpay-va-box">
                <div class="jpay-va-label">Nomor Virtual Account</div>
                <div class="jpay-va-number">110{{ str_pad($accountOrder->id, 8, '0', STR_PAD_LEFT) }}</div>
                <button class="jpay-copy-btn" data-copy="110{{ str_pad($accountOrder->id, 8, '0', STR_PAD_LEFT) }}">Salin Nomor VA</button>
              </div>
              <p class="jpay-method-hint">Bayar melalui mobile banking / ATM / Internet Banking</p>
            </div>

            <div class="jpay-method-trans-id">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--text-mute)" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
              <span class="jpay-trans-id-label">No. Pesanan</span>
              <span class="jpay-trans-id-value">#ORD-{{ str_pad($accountOrder->id, 6, '0', STR_PAD_LEFT) }}</span>
              <button class="jpay-copy-btn" data-copy="#ORD-{{ str_pad($accountOrder->id, 6, '0', STR_PAD_LEFT) }}">Salin</button>
            </div>
            <button class="jpay-next-btn" id="jpayNextBtn">Langkah Selanjutnya →</button>
          </div>
          <div class="jpay-method-success" id="jpayMethodSuccess" style="display:none">
            <div class="jpay-success-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="3" width="56" height="56"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
            <div class="jpay-success-label">Pembayaran Berhasil</div>
            <div class="jpay-success-sub">Pesanan sedang diproses. Admin akan menghubungi kamu via WhatsApp.</div>
          </div>
          <div class="jpay-method-failed" id="jpayMethodFailed" style="display:none">
            <div class="jpay-failed-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2.5" width="56" height="56"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div class="jpay-failed-label">Pembayaran Gagal</div>
            <div class="jpay-failed-sub">Silakan coba lagi atau hubungi admin</div>
          </div>
        </div>
      </div>
    </div>

    <div class="jpay-right">
      <div class="jpay-card jpay-card-sticky">
        <div class="jpay-card-head">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
          <span>Ringkasan Pemesanan</span>
        </div>
        <div class="jpay-ringkasan">
          <div class="jpay-ringkasan-product">
            <div class="jpay-ringkasan-icon">
              @if($listing->photo_url)
                <img src="{{ $listing->photo_url }}" alt="{{ $listing->product_name }}" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">
              @else
                <svg width="24" height="24" viewBox="0 0 32 32" fill="none"><defs><linearGradient id="rg" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#9d5cf5"/><stop offset="100%" stop-color="#4c1d95"/></linearGradient></defs><polygon points="16,2 27,11 22,30 10,30 5,11" fill="url(#rg)"/></svg>
              @endif
            </div>
            <div>
              <div class="jpay-ringkasan-game">{{ $listing->game }}</div>
              <div class="jpay-ringkasan-pkg">{{ $listing->product_name }}</div>
            </div>
          </div>
          <div class="jpay-ringkasan-details">
            <div class="jpay-ringkasan-row">
              <span class="jpay-r-label">Pemesan</span>
              <span class="jpay-r-value">{{ $accountOrder->customer_name }}</span>
            </div>
            <div class="jpay-ringkasan-row">
              <span class="jpay-r-label">Email</span>
              <span class="jpay-r-value">{{ $accountOrder->customer_email }}</span>
            </div>
            <div class="jpay-ringkasan-row">
              <span class="jpay-r-label">No. WhatsApp</span>
              <span class="jpay-r-value">{{ $accountOrder->customer_phone }}</span>
            </div>
            <div class="jpay-ringkasan-row">
              <span class="jpay-r-label">Metode Bayar</span>
              <span class="jpay-r-value">{{ $accountOrder->payment_method }}</span>
            </div>
            <div class="jpay-ringkasan-row">
              <span class="jpay-r-label">Status</span>
              <span class="jpay-status-dot">
                <span class="jpay-dot pending"></span>
                <span>Pending</span>
              </span>
            </div>
            <div class="jpay-ringkasan-row">
              <span class="jpay-r-label">Tanggal</span>
              <span class="jpay-r-value">{{ $accountOrder->created_at->format('d M Y, H:i') }}</span>
            </div>
          </div>
          <div class="jpay-rincian">
            <div class="jpay-rincian-row">
              <span>Harga Akun</span>
              <span>Rp {{ number_format($listing->price, 0, ',', '.') }}</span>
            </div>
            <div class="jpay-rincian-row">
              <span>Biaya Layanan</span>
              <span class="jpay-green">Gratis</span>
            </div>
            <div class="jpay-rincian-total">
              <span>Total Pembayaran</span>
              <span class="jpay-total-price">Rp {{ number_format($accountOrder->total_price, 0, ',', '.') }}</span>
            </div>
          </div>
        </div>
        <div class="jpay-ringkasan-actions">
          <a href="{{ route('jual-beli-akun') }}" class="btn btn-outline btn-full">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Kembali ke Jual Beli Akun
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.jpay-wrap {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1.5rem 4rem;
}
.jpay-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}
.jpay-header-badge {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  background: linear-gradient(135deg, var(--purple-light), var(--purple));
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 6px 20px -4px var(--purple-glow);
}
.jpay-header-badge svg { color: #fff; }
.jpay-title {
  font-size: 1.3rem;
  font-weight: 700;
  line-height: 1.2;
}
.jpay-sub {
  font-size: .82rem;
  color: var(--text-mute);
  margin-top: .1rem;
}

.jpay-demo-banner {
  display: flex;
  align-items: center;
  gap: .6rem;
  padding: .7rem 1rem;
  background: rgba(251, 191, 36, .1);
  border: 1px solid rgba(251, 191, 36, .25);
  border-radius: 10px;
  font-size: .78rem;
  color: #fbbf24;
  margin-bottom: 1.5rem;
}
.jpay-demo-banner svg { flex-shrink: 0; }

.jpay-grid {
  display: grid;
  grid-template-columns: 1.3fr 1fr;
  gap: 1.5rem;
  align-items: start;
}
@media (max-width: 920px) {
  .jpay-grid { grid-template-columns: 1fr; }
}

.jpay-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 16px;
  overflow: hidden;
  margin-bottom: 1.2rem;
}
.jpay-card-sticky {
  position: sticky;
  top: 2rem;
}
.jpay-card-head {
  display: flex;
  align-items: center;
  gap: .55rem;
  padding: .9rem 1.2rem;
  border-bottom: 1px solid var(--border);
  font-size: .88rem;
  font-weight: 700;
  color: var(--text);
}
.jpay-card-head svg { color: var(--purple-light); flex-shrink: 0; }

.jpay-status {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.2rem;
  gap: .8rem;
}
.jpay-status-left {
  display: flex;
  align-items: center;
  gap: .8rem;
}
.jpay-status-icon { flex-shrink: 0; line-height: 0; }
.jpay-status-label {
  font-weight: 700;
  font-size: .95rem;
}
.jpay-status-sub {
  font-size: .75rem;
  color: var(--text-mute);
  margin-top: .1rem;
}
.jpay-status-pill {
  display: inline-block;
  padding: .25rem .7rem;
  border-radius: 6px;
  font-size: .7rem;
  font-weight: 700;
  background: rgba(251, 191, 36, .15);
  color: #fbbf24;
  white-space: nowrap;
}
.jpay-countdown {
  display: flex;
  align-items: center;
  gap: .4rem;
  padding: .7rem 1.2rem;
  border-top: 1px solid var(--border);
  font-size: .72rem;
  color: var(--text-mute);
}
.jpay-countdown-items {
  display: inline-flex;
  align-items: center;
  gap: .15rem;
  margin-left: .15rem;
}
.jpay-cd-num {
  font-family: var(--font-display);
  font-weight: 700;
  font-size: .85rem;
  color: var(--text);
  min-width: 1.6em;
  text-align: center;
}
.jpay-cd-lbl {
  font-size: .6rem;
  text-transform: uppercase;
  color: var(--text-mute);
}
.jpay-cd-sep {
  color: var(--text-mute);
  margin: 0 .1rem;
}

.jpay-method-placeholder {
  padding: 2.4rem 1.5rem;
  text-align: center;
}
.jpay-method-illus { margin-bottom: .8rem; }
.jpay-method-placeholder-text {
  font-size: .82rem;
  color: var(--text-mute);
  margin-bottom: 1.2rem;
}
.jpay-method-result {
  padding: 1.2rem;
}
.jpay-method-result-head {
  display: flex;
  align-items: center;
  gap: .7rem;
  margin-bottom: 1.2rem;
}
.jpay-method-result-icon { flex-shrink: 0; line-height: 0; }
.jpay-method-result-label {
  font-size: .72rem;
  color: var(--text-mute);
}
.jpay-method-result-name {
  font-weight: 700;
  font-size: .9rem;
}
.jpay-method-qr-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 1rem;
}
.jpay-method-qr {
  background: #fff;
  border-radius: 10px;
  padding: 10px;
  display: inline-flex;
  margin-bottom: .5rem;
}
.jpay-qr-dummy svg { display: block; }
.jpay-method-hint {
  font-size: .72rem;
  color: var(--text-mute);
  text-align: center;
  max-width: 280px;
}
.jpay-method-va-wrap {
  margin-bottom: 1rem;
}
.jpay-va-box {
  background: var(--bg-soft);
  border: 1px dashed var(--border-strong);
  border-radius: 12px;
  padding: 1rem;
  text-align: center;
  margin-bottom: .5rem;
}
.jpay-va-label {
  font-size: .72rem;
  color: var(--text-mute);
  margin-bottom: .3rem;
}
.jpay-va-number {
  font-family: var(--font-display);
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--text);
  letter-spacing: .05em;
  margin-bottom: .6rem;
}
.jpay-copy-btn {
  display: inline-flex;
  align-items: center;
  gap: .3rem;
  padding: .4rem .9rem;
  border: 1px solid var(--border-strong);
  border-radius: 8px;
  background: var(--surface-2);
  color: var(--text-dim);
  font-size: .75rem;
  font-weight: 600;
  cursor: pointer;
  transition: all .2s;
  font-family: var(--font-body);
}
.jpay-copy-btn:hover {
  border-color: var(--purple-light);
  color: var(--purple-light);
  background: rgba(124, 58, 237, .1);
}
.jpay-method-trans-id {
  display: flex;
  align-items: center;
  gap: .4rem;
  padding: .7rem .9rem;
  background: var(--bg-soft);
  border-radius: 10px;
  font-size: .75rem;
  flex-wrap: wrap;
}
.jpay-trans-id-label {
  color: var(--text-mute);
}
.jpay-trans-id-value {
  font-weight: 700;
  color: var(--text);
  font-family: var(--font-display);
  letter-spacing: .02em;
  margin-right: auto;
}
.jpay-method-trans-id .jpay-copy-btn {
  padding: .25rem .6rem;
  font-size: .68rem;
}

.jpay-method-success,
.jpay-method-failed {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 2rem 1.5rem;
  text-align: center;
}
.jpay-success-icon,
.jpay-failed-icon {
  margin-bottom: .8rem;
}
.jpay-success-label {
  font-weight: 800;
  font-size: 1.1rem;
  color: #34d399;
}
.jpay-success-sub {
  font-size: .82rem;
  color: var(--text-dim);
  margin-top: .25rem;
}
.jpay-failed-label {
  font-weight: 800;
  font-size: 1.1rem;
  color: #f87171;
}
.jpay-failed-sub {
  font-size: .82rem;
  color: var(--text-dim);
  margin-top: .25rem;
}

.jpay-ringkasan {
  display: flex;
  flex-direction: column;
}
.jpay-ringkasan-product {
  display: flex;
  align-items: center;
  gap: .7rem;
  padding: 1rem 1.1rem;
  border-bottom: 1px solid var(--border);
}
.jpay-ringkasan-icon {
  width: 44px;
  height: 44px;
  border-radius: 8px;
  background: var(--bg-soft);
  overflow: hidden;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}
.jpay-ringkasan-game {
  font-size: .72rem;
  color: var(--text-mute);
}
.jpay-ringkasan-pkg {
  font-weight: 700;
  font-size: .88rem;
  line-height: 1.2;
}
.jpay-ringkasan-details {
  padding: .6rem 1.1rem;
}
.jpay-ringkasan-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: .35rem 0;
  font-size: .78rem;
  gap: .5rem;
}
.jpay-r-label {
  color: var(--text-mute);
  white-space: nowrap;
}
.jpay-r-value {
  color: var(--text);
  font-weight: 500;
  text-align: right;
  word-break: break-all;
}
.jpay-status-dot {
  display: inline-flex;
  align-items: center;
  gap: .3rem;
  font-size: .78rem;
}
.jpay-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  flex-shrink: 0;
}
.jpay-dot.pending { background: #fbbf24; box-shadow: 0 0 6px rgba(251,191,36,.5); }
.jpay-dot.processing { background: #60a5fa; box-shadow: 0 0 6px rgba(96,165,250,.5); }
.jpay-dot.success { background: #34d399; box-shadow: 0 0 6px rgba(52,211,153,.5); }
.jpay-dot.failed { background: #f87171; box-shadow: 0 0 6px rgba(248,113,113,.5); }

.jpay-rincian {
  border-top: 1px solid var(--border);
  padding: .8rem 1.1rem;
}
.jpay-rincian-row {
  display: flex;
  justify-content: space-between;
  font-size: .78rem;
  color: var(--text-dim);
  padding: .25rem 0;
}
.jpay-green { color: #34d399; }
.jpay-rincian-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: .4rem;
  padding-top: .6rem;
  border-top: 1px solid var(--border);
  font-weight: 700;
  font-size: .88rem;
}
.jpay-total-price {
  font-family: var(--font-display);
  color: var(--purple-light);
  font-size: 1.15rem;
}
.jpay-next-btn {
  display: block;
  width: 100%;
  margin-top: .8rem;
  padding: .7rem 1rem;
  border: 1.5px dashed var(--border-strong);
  border-radius: 10px;
  background: transparent;
  color: var(--text-dim);
  font-size: .82rem;
  font-weight: 600;
  cursor: pointer;
  transition: all .2s;
  font-family: var(--font-body);
  text-align: center;
}
.jpay-next-btn:hover {
  border-color: var(--purple-light);
  color: var(--purple-light);
  background: rgba(124,58,237,.06);
}

.jpay-ringkasan-actions {
  padding: .8rem 1.1rem;
  border-top: 1px solid var(--border);
}

/* ===== animations ===== */
.spin-slow {
  animation: spin 1.2s linear infinite;
}
@keyframes spin {
  100% { transform: rotate(360deg); }
}
</style>

@push('scripts')
<script>
(function() {
  const placeholder = document.getElementById('jpayMethodPlaceholder');
  const result = document.getElementById('jpayMethodResult');
  const success = document.getElementById('jpayMethodSuccess');
  const failed = document.getElementById('jpayMethodFailed');
  const showBtn = document.getElementById('jpayShowBtn');

  if (showBtn) {
    showBtn.addEventListener('click', function() {
      showBtn.disabled = true;
      showBtn.innerHTML = '<svg class="spin-slow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg> Memproses...';

      setTimeout(function() {
        placeholder.style.display = 'none';
        result.style.display = 'block';
        showBtn.style.display = 'none';
      }, 1200);
    });
  }

  // countdown timer (24 hours from created_at)
  (function() {
    const created = new Date(@json($accountOrder->created_at->toIso8601String()));
    const expires = new Date(created.getTime() + 24 * 60 * 60 * 1000);

    function tick() {
      const diff = Math.max(0, expires - new Date());
      const h = Math.floor(diff / 3600000);
      const m = Math.floor((diff % 3600000) / 60000);
      const s = Math.floor((diff % 60000) / 1000);
      document.getElementById('countHours').textContent = String(h).padStart(2, '0');
      document.getElementById('countMinutes').textContent = String(m).padStart(2, '0');
      document.getElementById('countSeconds').textContent = String(s).padStart(2, '0');
    }
    tick();
    setInterval(tick, 1000);
  })();

  // dummy next step
  const nextBtn = document.getElementById('jpayNextBtn');
  if (nextBtn) {
    nextBtn.addEventListener('click', function() {
      document.getElementById('jpayMethodPlaceholder').style.display = 'none';
      document.getElementById('jpayMethodResult').style.display = 'none';
      document.getElementById('jpayMethodSuccess').style.display = 'flex';
      document.getElementById('jpayMethodFailed').style.display = 'none';
      document.getElementById('jpayStatusPill').textContent = 'BERHASIL';
      document.getElementById('jpayStatusPill').style.background = 'rgba(52,211,153,.15)';
      document.getElementById('jpayStatusPill').style.color = '#34d399';
      document.getElementById('jpayCountdown').style.display = 'none';
      document.querySelector('#jpayStatusBody .jpay-status-icon').innerHTML =
        '<svg viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="2.5" width="44" height="44"><path d="M20 6 9 17l-5-5"/></svg>';
      document.querySelector('#jpayStatusBody .jpay-status-label').textContent = 'Pembayaran Berhasil';
      document.querySelector('#jpayStatusBody .jpay-status-sub').textContent = 'Pesanan sedang diproses';
    });
  }

  // copy button
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.jpay-copy-btn');
    if (!btn) return;
    const text = btn.dataset.copy;
    if (!text) return;
    navigator.clipboard.writeText(text).then(function() {
      const orig = btn.textContent;
      btn.textContent = 'Tersalin!';
      btn.style.background = 'rgba(52,211,153,.2)';
      btn.style.color = '#34d399';
      setTimeout(function() {
        btn.textContent = orig;
        btn.style.background = '';
        btn.style.color = '';
      }, 2000);
    });
  });
})();
</script>
@endpush
@endsection
