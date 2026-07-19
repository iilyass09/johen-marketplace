@extends('layouts.topup')

@section('title', 'Checkout - ' . $listing->product_name . ' - Johen Gaming')

@section('content')
<div class="jba-checkout-page">
  <div class="jba-checkout-wrap">
    <div class="jba-checkout-left">
      <a href="{{ route('jual-beli-akun.detail', $listing) }}" class="jba-checkout-back">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Kembali
      </a>

      <div class="jba-checkout-card">
        @if($listing->photo_url)
          <div class="jba-checkout-card-img">
            <img src="{{ $listing->photo_url }}" alt="{{ $listing->product_name }}">
          </div>
        @endif
        <div class="jba-checkout-card-body">
          <div class="jba-checkout-game">{{ $listing->game }}</div>
          <h2 class="jba-checkout-title">{{ $listing->product_name }}</h2>
          @if($listing->owner_name)
            <div class="jba-checkout-owner">👤 {{ $listing->owner_name }}</div>
          @endif
          <div class="jba-checkout-price">
            <span class="jba-checkout-price-label">Total Pembayaran</span>
            <span class="jba-checkout-price-value">Rp {{ number_format($listing->price, 0, ',', '.') }}</span>
          </div>
        </div>
      </div>

      <div class="jba-checkout-specs">
        <h3>Spesifikasi Produk</h3>
        <div class="jba-checkout-specs-content">
          {{ nl2br(e($listing->specifications)) }}
        </div>
      </div>
    </div>

    <div class="jba-checkout-right">
      <div class="jba-checkout-form-wrap">
        <h2 class="jba-checkout-form-title">Data Pemesan</h2>
        <p class="jba-checkout-form-sub">Isi data diri kamu untuk proses pemesanan akun.</p>

        <form method="POST" action="{{ route('jual-beli-akun.checkout.store', $listing) }}" class="jba-checkout-form">
          @csrf

          <div class="jba-checkout-field">
            <label for="customer_name">Nama Lengkap <span class="jba-req">*</span></label>
            <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', auth()->user()->name ?? '') }}" required placeholder="Masukkan nama lengkap">
            @error('customer_name')<span class="jba-field-error">{{ $message }}</span>@enderror
          </div>

          <div class="jba-checkout-field">
            <label for="customer_email">Email <span class="jba-req">*</span></label>
            <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email', auth()->user()->email ?? '') }}" required placeholder="Masukkan email">
            @error('customer_email')<span class="jba-field-error">{{ $message }}</span>@enderror
          </div>

          <div class="jba-checkout-field">
            <label for="customer_phone">No. WhatsApp <span class="jba-req">*</span></label>
            <input type="tel" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" required placeholder="08xxxxxxxxxx">
            @error('customer_phone')<span class="jba-field-error">{{ $message }}</span>@enderror
          </div>

          <div class="jba-checkout-field">
            <label>Metode Pembayaran <span class="jba-req">*</span></label>
            <div class="jba-payment-grid">
              @forelse($paymentMethods as $pm)
              <label class="jba-pay-opt">
                <input type="radio" name="payment_method" value="{{ $pm->name }}" {{ old('payment_method') === $pm->name ? 'checked' : ($loop->first ? 'checked' : '') }} required>
                <div class="jba-pay-opt-content">
                  @if($pm->photo_url)
                    <img src="{{ $pm->photo_url }}" alt="{{ $pm->name }}" class="jba-pay-img">
                  @elseif($pm->icon)
                    <span class="jba-pay-icon">{{ $pm->icon }}</span>
                  @else
                    <span class="jba-pay-name">{{ $pm->name }}</span>
                  @endif
                  <span class="jba-pay-label">{{ $pm->name }}</span>
                </div>
              </label>
              @empty
              <div class="jba-pay-empty">Metode pembayaran tidak tersedia</div>
              @endforelse
            </div>
            @error('payment_method')<span class="jba-field-error">{{ $message }}</span>@enderror
          </div>

          <div class="jba-checkout-field">
            <label for="notes">Catatan (opsional)</label>
            <textarea name="notes" id="notes" rows="3" placeholder="Catatan tambahan untuk penjual...">{{ old('notes') }}</textarea>
            @error('notes')<span class="jba-field-error">{{ $message }}</span>@enderror
          </div>

          <button type="submit" class="jba-checkout-submit">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            Buat Pesanan
          </button>
        </form>

      </div>
    </div>
  </div>
</div>

<style>
.jba-checkout-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1.5rem 4rem;
}
.jba-checkout-wrap {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  align-items: start;
}
@media (max-width: 768px) {
  .jba-checkout-wrap { grid-template-columns: 1fr; }
}

.jba-checkout-back {
  display: inline-flex;
  align-items: center;
  gap: .35rem;
  font-size: .82rem;
  color: var(--text-dim);
  margin-bottom: 1.2rem;
  transition: color .2s;
}
.jba-checkout-back:hover { color: var(--purple-light); }

.jba-checkout-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 16px;
  overflow: hidden;
  margin-bottom: 1.2rem;
}
.jba-checkout-card-img {
  width: 100%;
  aspect-ratio: 16/9;
  background: var(--bg-soft);
  overflow: hidden;
}
.jba-checkout-card-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.jba-checkout-card-body {
  padding: 1.2rem;
}
.jba-checkout-game {
  font-size: .75rem;
  color: var(--purple-light);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .03em;
}
.jba-checkout-title {
  font-size: 1.15rem;
  font-weight: 700;
  margin: .2rem 0 .25rem;
  line-height: 1.2;
}
.jba-checkout-owner {
  font-size: .82rem;
  color: var(--text-dim);
  margin-bottom: .6rem;
}
.jba-checkout-price {
  display: flex;
  flex-direction: column;
  border-top: 1px solid var(--border);
  padding-top: .8rem;
  margin-top: .4rem;
}
.jba-checkout-price-label {
  font-size: .75rem;
  color: var(--text-mute);
  font-weight: 500;
}
.jba-checkout-price-value {
  font-family: var(--font-display);
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--gold);
  line-height: 1.2;
}

.jba-checkout-specs {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 1.2rem;
}
.jba-checkout-specs h3 {
  font-size: .88rem;
  font-weight: 700;
  margin-bottom: .5rem;
}
.jba-checkout-specs-content {
  font-size: .82rem;
  color: var(--text-dim);
  line-height: 1.7;
  white-space: pre-line;
}

.jba-checkout-form-wrap {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 1.5rem;
}
.jba-checkout-form-title {
  font-size: 1.15rem;
  font-weight: 700;
}
.jba-checkout-form-sub {
  font-size: .82rem;
  color: var(--text-mute);
  margin: .2rem 0 1.2rem;
}
.jba-checkout-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
.jba-checkout-field label {
  display: block;
  font-size: .82rem;
  font-weight: 600;
  color: var(--text-dim);
  margin-bottom: .35rem;
}
.jba-req { color: #f87171; }
.jba-checkout-field input[type="text"],
.jba-checkout-field input[type="email"],
.jba-checkout-field input[type="tel"],
.jba-checkout-field textarea {
  width: 100%;
  background: var(--bg-soft);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: .7rem .9rem;
  color: var(--text);
  font-size: .85rem;
  font-family: var(--font-body);
  transition: border-color .2s;
  box-sizing: border-box;
}
.jba-checkout-field input:focus,
.jba-checkout-field textarea:focus {
  outline: none;
  border-color: var(--purple-light);
}
.jba-checkout-field textarea {
  resize: vertical;
  min-height: 80px;
}
.jba-field-error {
  display: block;
  font-size: .72rem;
  color: #f87171;
  margin-top: .25rem;
}

.jba-payment-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: .5rem;
}
.jba-pay-opt {
  cursor: pointer;
}
.jba-pay-opt input {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}
.jba-pay-opt-content {
  display: flex;
  align-items: center;
  gap: .5rem;
  padding: .6rem .7rem;
  border: 1.5px solid var(--border);
  border-radius: 10px;
  background: var(--bg-soft);
  transition: border-color .2s, background .2s;
}
.jba-pay-opt input:checked + .jba-pay-opt-content {
  border-color: var(--purple-light);
  background: rgba(124, 58, 237, .12);
}
.jba-pay-opt:hover .jba-pay-opt-content {
  border-color: var(--border-strong);
}
.jba-pay-img {
  width: 28px;
  height: 28px;
  object-fit: contain;
  border-radius: 4px;
  flex-shrink: 0;
}
.jba-pay-icon {
  font-size: 1.2rem;
  flex-shrink: 0;
  line-height: 1;
}
.jba-pay-label {
  font-size: .78rem;
  font-weight: 600;
  color: var(--text);
}
.jba-pay-empty {
  grid-column: 1 / -1;
  text-align: center;
  color: var(--text-mute);
  font-size: .82rem;
  padding: .8rem;
}

.jba-checkout-submit {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: .5rem;
  width: 100%;
  padding: .8rem 1.5rem;
  border: none;
  border-radius: 12px;
  background: linear-gradient(135deg, var(--purple-light), var(--purple));
  color: #fff;
  font-weight: 700;
  font-size: .92rem;
  cursor: pointer;
  transition: transform .2s, box-shadow .2s;
  box-shadow: 0 4px 20px -4px var(--purple-glow);
  font-family: var(--font-body);
  margin-top: .5rem;
}
.jba-checkout-submit:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 36px -6px rgba(124, 58, 237, .65);
}

.jba-checkout-info {
  display: flex;
  align-items: flex-start;
  gap: .5rem;
  margin-top: 1rem;
  padding: .8rem;
  background: rgba(124, 58, 237, .08);
  border: 1px solid rgba(124, 58, 237, .2);
  border-radius: 10px;
  font-size: .75rem;
  color: var(--text-dim);
  line-height: 1.5;
}
.jba-checkout-info svg {
  flex-shrink: 0;
  color: var(--purple-light);
  margin-top: 2px;
}
</style>
@endsection
