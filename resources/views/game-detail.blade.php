@extends('layouts.topup')

@php $pageType = $brand->service_type === 'joki' ? 'Joki' : 'Top Up'; @endphp
@section('title', $brand->name . ' — ' . $pageType . ' ' . $brand->name)

@php
  $grouped = $products->groupBy('type');
  $regions = collect();
  $itemGroups = collect();

  foreach (['First Topup (Double Diamonds)', 'Special Items'] as $specialType) {
      if ($grouped->has($specialType)) {
          $itemGroups->put($specialType, $grouped->get($specialType));
          $grouped->forget($specialType);
      }
  }

  $instantKey = null;
  foreach (['instant', 'Instant'] as $key) {
      if ($grouped->has($key)) { $instantKey = $key; break; }
  }
  if ($instantKey) {
      $regions = $grouped->get($instantKey)->groupBy('region');
  }
  $selectedRegion = $regions->isNotEmpty() ? ($regions->has('ID') ? 'ID' : ($regions->has('') ? 'ALL' : $regions->keys()->first())) : null;
  $firstProduct = $products->first();
  $payData = $paymentMethods->map(fn($m) => [
      'key' => $m->code,
      'title' => $m->name,
      'category' => $m->category,
      'photo' => $m->photo_url ?? null,
      'fee' => 0,
  ])->values();
  $categories = [
      'qris' => 'QRIS',
      'ewallet' => 'E-Wallet',
      'va' => 'Virtual Account',
      'convenience_store' => 'Convenience Store',
  ];
  $groupedPay = $paymentMethods->groupBy('category');
@endphp

@section('content')
<!-- ===== BANNER (background only) ===== -->
<section class="game-hero">
  <div class="game-hero-bg"@if($brand->detail_bg_url) style="background-image:url('{{ $brand->detail_bg_url }}');background-position:{{ $brand->detail_bg_position ?? 'center' }}"@endif></div>
  <div class="game-hero-overlay"></div>
</section>

<!-- ===== THUMBNAIL (overlap banner + content) ===== -->
<div class="gd-header-wrap">
  <div class="gd-header-inner">
    <div class="gd-thumb">
      @if($brand->thumbnail_url)
        <img src="{{ $brand->thumbnail_url }}" alt="{{ $brand->name }}">
      @else
        <span style="font-size:2.5rem">{{ $brand->icon ?? '🎮' }}</span>
      @endif
    </div>
    <div class="gd-header-meta">
      <h1>{{ $brand->name }}</h1>
      @if($brand->category)
        <span class="gd-header-cat">{{ ucfirst($brand->category) }}</span>
      @endif
      <div class="gd-header-badges">
        <span class="gd-header-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h7l-1 8 10-12h-7z"/></svg>
          Proses Cepat
        </span>
        <span class="gd-header-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
          Layanan 24/7
        </span>
        <span class="gd-header-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          Transaksi Aman
        </span>
      </div>
    </div>
  </div>
</div>

<div class="gd-wrap">
  <div class="gd-detail-grid">
    <div class="gd-detail-main">
  <!-- ===== STEP 1: Data Akun ===== -->
  <div class="gd-step">
    <div class="gd-step-head"><div class="gd-step-num">1</div><div class="gd-step-title">Masukan Data Akun</div></div>
    <div class="gd-step1-grid">
      <div class="gd-step1-left">
        <div class="gd-card">
          <div class="gd-field-row">
            <div class="gd-field">
              <label for="userId">Email atau User ID</label>
              <input type="text" id="userId" placeholder="12345678">
              <div class="gd-field-error" id="userIdError">User ID wajib diisi.</div>
            </div>
            <div class="gd-field">
              <label for="zoneId">Zone ID</label>
              <input type="text" id="zoneId" placeholder="(1234)">
              <div class="gd-field-error" id="zoneIdError">Zone ID wajib diisi.</div>
            </div>
          </div>
          <p class="gd-field-hint">To find your User ID, tap on your avatar in the top-left corner of the main game screen.</p>
        </div>

      </div>
    </div>
  </div>

  <!-- ===== STEP 2: Pilih Nominal ===== -->
  <div class="gd-step-lock-group" id="productLock">
  <div class="gd-step">
    <div class="gd-step-head"><div class="gd-step-num">2</div><div class="gd-step-title">Pilih Nominal</div></div>

    {{-- Item type groups (Special Items, First Topup, etc.) --}}
    @foreach($itemGroups as $itemType => $typeItems)
      <div class="gd-group">
        <div class="gd-group-head">
          <div class="gd-group-title">{{ $itemType }} <span class="gd-spark">✨</span></div>
        </div>
        <div class="gd-pkg-grid gd-pkg-instant" data-type="{{ Str::slug($itemType) }}" data-no-region-filter="true">
          @foreach($typeItems as $p)
            <button type="button"
              class="gd-pkg-card"
              data-id="{{ $p->id }}"
              data-label="{{ $p->product_name }}"
              data-price="{{ $p->selling_price }}"
              data-region="{{ $p->region ?: 'ALL' }}">
              @if($p->photo_url)
                <img class="gd-pkg-img" src="{{ $p->photo_url }}" alt="{{ $p->product_name }}">
              @else
                <svg class="gd-gem" viewBox="0 0 32 32" width="30" height="30">
                  <defs><linearGradient id="gemGrad" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="var(--purple-light)"/><stop offset="100%" stop-color="var(--purple-dark)"/></linearGradient></defs>
                  <polygon points="16,2 27,11 22,30 10,30 5,11" fill="url(#gemGrad)"/>
                  <polygon points="16,2 27,11 16,14" fill="#e4d9ff" opacity=".55"/>
                  <polygon points="16,2 5,11 16,14" fill="#f4eeff" opacity=".8"/>
                  <polygon points="5,11 16,14 10,30" fill="#6d33d6" opacity=".7"/>
                  <polygon points="27,11 16,14 22,30" fill="#4c1d95" opacity=".75"/>
                </svg>
              @endif
              <span class="gd-pkg-info">
                <span class="gd-pkg-amt">{{ $p->product_name }}</span>
                <span class="gd-pkg-price">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</span>
              </span>
              <span class="gd-pkg-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span>
            </button>
          @endforeach
        </div>
      </div>
    @endforeach

    {{-- Default type groups (region-based for instant, plain for joki) --}}
    @foreach($grouped as $type => $items)
      @if($items->isEmpty()) @continue @endif
      @php
        $typeKey = Str::slug($type);
        $isInstant = strtolower($type) === 'instant';
      @endphp
      <div class="gd-group">
        <div class="gd-group-head">
          <div class="gd-group-title">{{ ucwords($type) }} <span class="gd-spark">✨</span></div>
          @if($isInstant && $regions->count() > 1)
            <div class="gd-region-tabs" data-group="{{ $typeKey }}">
              @foreach($regions->keys() as $r)
                <button class="gd-region-btn{{ $r === $selectedRegion ? ' active' : '' }}" data-region="{{ $r ?: 'ALL' }}">{{ $r === 'ID' ? 'Indonesia' : ($r === 'MY' ? 'Malaysia' : ($r === '' ? 'All Region' : $r)) }} ({{ $r ?: 'ALL' }})</button>
              @endforeach
            </div>
          @endif
        </div>
        <div class="gd-pkg-grid{{ $isInstant ? ' gd-pkg-instant' : '' }}"
             data-type="{{ $typeKey }}"
             @if($isInstant) data-has-regions="true"@endif>
          @foreach($items as $p)
            <button type="button"
              class="gd-pkg-card"
                data-id="{{ $p->id }}"
                data-label="{{ $p->product_name }}"
              data-price="{{ $p->selling_price }}"
              @if($isInstant) data-region="{{ $p->region ?: 'ALL' }}"@endif>
              @if($p->photo_url)
                <img class="gd-pkg-img" src="{{ $p->photo_url }}" alt="{{ $p->product_name }}">
              @else
                <svg class="gd-gem" viewBox="0 0 32 32" width="30" height="30">
                  <defs><linearGradient id="gemGrad" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="var(--purple-light)"/><stop offset="100%" stop-color="var(--purple-dark)"/></linearGradient></defs>
                  <polygon points="16,2 27,11 22,30 10,30 5,11" fill="url(#gemGrad)"/>
                  <polygon points="16,2 27,11 16,14" fill="#e4d9ff" opacity=".55"/>
                  <polygon points="16,2 5,11 16,14" fill="#f4eeff" opacity=".8"/>
                  <polygon points="5,11 16,14 10,30" fill="#6d33d6" opacity=".7"/>
                  <polygon points="27,11 16,14 22,30" fill="#4c1d95" opacity=".75"/>
                </svg>
              @endif
              <span class="gd-pkg-info">
                <span class="gd-pkg-amt">{{ $p->product_name }}</span>
                <span class="gd-pkg-price">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</span>
              </span>
              <span class="gd-pkg-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span>
            </button>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>

  <!-- ===== STEP 3: Quantity ===== -->
  <div class="gd-step">
    <div class="gd-step-head"><div class="gd-step-num">3</div><div class="gd-step-title">Masukan Jumlah Pembelian</div></div>
    <div class="gd-qty-row">
      <button class="gd-qty-btn" id="qtyMinus">–</button>
      <input type="text" id="qtyInput" value="1" inputmode="numeric">
      <button class="gd-qty-btn gd-qty-plus" id="qtyPlus">+</button>
    </div>
  </div>
  </div>

  <!-- ===== STEP 4: Pembayaran ===== -->
  <div class="gd-step">
    <div class="gd-step-head"><div class="gd-step-num">4</div><div class="gd-step-title">Pilih Pembayaran</div></div>
    <div class="gd-pay-group" id="payGroup">
      @foreach($categories as $catKey => $catLabel)
        @php $catMethods = $groupedPay->get($catKey, collect()); @endphp
        <div class="gd-pay-category" data-category="{{ $catKey }}">
          <button type="button" class="gd-pay-cat-head">
            <span class="gd-pay-cat-label">{{ $catLabel }}</span>
            <span class="gd-pay-cat-logos">
              @foreach($catMethods as $pm)
                <span class="gd-pay-cat-logo">
                  @if($pm->photo_url)
                    <img src="{{ $pm->photo_url }}" alt="{{ $pm->name }}"@if($pm->photo_light_url) data-light="{{ $pm->photo_light_url }}"@endif>
                  @endif
                </span>
              @endforeach
            </span>
            <svg class="gd-pay-cat-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
          </button>
          <div class="gd-pay-cat-body">
            @foreach($catMethods as $pm)
              <div class="gd-pay-row" data-key="{{ $pm->code }}" data-category="{{ $catKey }}">
                <button type="button" class="gd-pay-row-head">
                  <span class="gd-pay-icon">
                    @if($pm->photo_url)
                      <img src="{{ $pm->photo_url }}" alt="{{ $pm->name }}" class="pay-badge-img"@if($pm->photo_light_url) data-light="{{ $pm->photo_light_url }}"@endif>
                    @else
                      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                    @endif
                  </span>
                  <span class="gd-pay-label"><span class="gd-pay-t">{{ $pm->name }}</span></span>
                  <span class="gd-pay-radio"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span>
                </button>
              </div>
            @endforeach
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- ===== STEP 5: Promo ===== -->
  <div class="gd-step">
    <div class="gd-step-head"><div class="gd-step-num">5</div><div class="gd-step-title">Kode Promo</div></div>
    <div class="gd-card">
      <div class="gd-field">
        <label for="promoInput">Masukan Kode Promo</label>
        <input type="text" id="promoInput" placeholder="JOHENI10">
      </div>
      <button class="btn btn-solid btn-full" id="promoBtn" style="margin-top:.7rem">Terapkan</button>
      <p class="gd-promo-msg" id="promoMsg"></p>
    </div>
  </div>

  <!-- ===== STEP 6: Kontak ===== -->
  <div class="gd-step">
    <div class="gd-step-head"><div class="gd-step-num">6</div><div class="gd-step-title">Detail Kontak</div></div>
    <div class="gd-card">
      <div class="gd-contact-grid">
        <div class="gd-field">
          <label for="emailInput">Email</label>
          <input type="email" id="emailInput" placeholder="example@gmail.com">
          <div class="gd-field-error" id="emailError">Masukan email yang valid.</div>
        </div>
        <div class="gd-field">
          <label for="waInput">WhatsApp Number</label>
          <div class="gd-wa-input">
            <span class="gd-wa-prefix">+62</span>
            <input type="tel" id="waInput" placeholder="812xxxxxxx">
          </div>
          <div class="gd-field-error" id="waError">Nomor WhatsApp wajib diisi.</div>
        </div>
      </div>
      <p class="gd-field-hint">*Nomor ini akan dihubungi jika terjadi masalah.</p>
    </div>
  </div>

    </div>
    <div class="gd-detail-side">
      <div class="gd-card">
        <div class="gd-review-section">
          <div class="gd-review-head">
            <h4>Ulasan & Rating</h4>
          </div>
          <div class="gd-review-body">
            <span class="gd-review-num">0.0</span>
            <span class="gd-stars">☆☆☆☆☆</span>
          </div>
        </div>
      </div>
      <div class="gd-card">
        <div class="gd-help-section">
          <strong>Butuh Bantuan?</strong>
          <p>Kamu Bisa Hubungi Admin <a href="#" class="gd-help-link" id="adminHelpLink">Disini</a></p>
        </div>
      </div>
      <div class="gd-card gd-summary-card" id="summaryCard">
        <div class="gd-summary-head">
          <div class="gd-summary-icon">
            @if($brand->thumbnail_url)
              <img src="{{ $brand->thumbnail_url }}" alt="{{ $brand->name }}">
            @else
              <span>{{ $brand->icon ?? '🎮' }}</span>
            @endif
          </div>
          <div>
            <p class="gd-summary-game">{{ $brand->name }}</p>
            <p class="gd-summary-pkg" id="sumPkgName">-</p>
          </div>
        </div>
        <div class="gd-summary-empty" id="sumEmpty">Tidak ada produk yang dipilih</div>
        <div class="gd-summary-details" id="sumDetails" style="display:none">
          <div class="gd-summary-line"><span>Harga</span><strong id="sumHarga">Rp 0</strong></div>
          <div class="gd-summary-line"><span>Jumlah Pembelian</span><strong id="sumQty">1</strong></div>
          <div class="gd-summary-line"><span>Biaya Layanan</span><strong id="sumFee">Rp 0</strong></div>
          <div class="gd-summary-total"><span>Total Pembayaran</span><span id="sumTotal">Rp 0</span></div>
        </div>
        <button class="btn btn-solid btn-full" id="orderNowBtn" style="margin-top:1rem">Pesan Sekarang</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
(function(){
'use strict';

const $ = (s, ctx) => (ctx||document).querySelector(s);
const $$ = (s, ctx) => Array.from((ctx||document).querySelectorAll(s));

const rupiah = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');

const brandName = @json($brand->name);
const paymentMethods = @json($payData);

let selectedPkg = null;
let selectedRegion = @json($selectedRegion);
let qty = 1;
let promoDiscount = 0;
let isAuth = @json(Auth::check());

/* ---------- auto-select from URL ---------- */
(function(){
    const params = new URLSearchParams(window.location.search);
    const preselectedId = params.get('product');
    if (preselectedId) {
        const card = document.querySelector('.gd-pkg-card[data-id="' + preselectedId + '"]');
        if (card) {
            setTimeout(function(){
                card.click();
                card.scrollIntoView({behavior:'smooth', block:'center'});
            }, 300);
        }
    }
})();

/* ---------- package click ---------- */
document.addEventListener('click', e => {
    const card = e.target.closest('.gd-pkg-card');
    if (!card) return;
    if (productLock.classList.contains('gd-step-locked')) {
        showToast('Isi Data Akun terlebih dahulu', false);
        userIdInput.scrollIntoView({behavior:'smooth', block:'center'});
        return;
    }
    if (card.closest('.gd-pkg-instant')) {
        const region = card.dataset.region;
        if (region && selectedRegion && region !== selectedRegion) return;
    }
    $$('.gd-pkg-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    selectedPkg = { id: Number(card.dataset.id), label: card.dataset.label, price: Number(card.dataset.price), region: card.dataset.region || null };
    updateSummary();
});

/* ---------- region tabs ---------- */
$$('.gd-region-tabs').forEach(tabs => {
    tabs.addEventListener('click', e => {
        const btn = e.target.closest('.gd-region-btn');
        if (!btn) return;
        $$('.gd-region-btn', tabs).forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        selectedRegion = btn.dataset.region;
        $$('.gd-pkg-card', tabs.closest('.gd-group')).forEach(c => {
            if (c.dataset.region) {
                c.style.display = c.dataset.region === selectedRegion ? '' : 'none';
            }
        });
        const visible = $$('.gd-pkg-card:not([style*="display:none"])', tabs.closest('.gd-group'));
        if (visible.length) {
            visible.forEach(c => c.classList.remove('selected'));
        }
    });
});

/* init: hide non-default region packages */
$$('.gd-pkg-instant').forEach(grid => {
    if (grid.dataset.noRegionFilter) return;
    $$('.gd-pkg-card', grid).forEach(c => {
        if (c.dataset.region && c.dataset.region !== selectedRegion) {
            c.style.display = 'none';
        }
    });
});

/* ---------- quantity ---------- */
const qtyInput = $('#qtyInput');
function setQty(v) {
    v = Math.max(1, Math.min(99, Math.round(v)||1));
    qty = v;
    qtyInput.value = v;
    $('#qtyMinus').disabled = v <= 1;
    updateSummary();
}
function qtyLockCheck() {
    if (productLock.classList.contains('gd-step-locked')) {
        showToast('Isi Data Akun terlebih dahulu', false);
        userIdInput.scrollIntoView({behavior:'smooth', block:'center'});
        return true;
    }
    return false;
}
$('#qtyMinus').addEventListener('click', () => { if (qtyLockCheck()) return; setQty(qty-1); });
$('#qtyPlus').addEventListener('click', () => { if (qtyLockCheck()) return; setQty(qty+1); });
qtyInput.addEventListener('change', () => { if (qtyLockCheck()) return; setQty(parseInt(qtyInput.value,10)); });
qtyInput.addEventListener('input', () => { qtyInput.value = qtyInput.value.replace(/[^0-9]/g,''); });

/* ---------- payment methods ---------- */
let selectedPayFee = 0;
let selectedPayLabel = '';
const payGroup = $('#payGroup');
const productLock = $('#productLock');
const userIdInput = $('#userId'), zoneIdInput = $('#zoneId');

function togglePayLock() {
    const unlocked = userIdInput.value.trim().length > 0 && zoneIdInput.value.trim().length > 0;
    payGroup.classList.toggle('gd-pay-group--locked', !unlocked);
    productLock.classList.toggle('gd-step-locked', !unlocked);
    if (!unlocked && selectedPkg) {
        $$('.gd-pkg-card').forEach(c => c.classList.remove('selected'));
        selectedPkg = null;
        updateSummary();
    }
}
userIdInput.addEventListener('input', togglePayLock);
zoneIdInput.addEventListener('input', togglePayLock);
togglePayLock();

/* category accordion toggle */
$('#payGroup').addEventListener('click', e => {
    if (payGroup.classList.contains('gd-pay-group--locked')) {
        showToast('Isi Data Akun terlebih dahulu', false);
        userIdInput.scrollIntoView({behavior:'smooth', block:'center'});
        return;
    }
    const catHead = e.target.closest('.gd-pay-cat-head');
    if (catHead) {
        const cat = catHead.closest('.gd-pay-category');
        const isOpen = cat.classList.contains('open');
        $$('.gd-pay-category').forEach(c => c.classList.remove('open'));
        if (!isOpen) cat.classList.add('open');
        return;
    }
    /* method select */
    const head = e.target.closest('.gd-pay-row-head');
    if (!head) return;
    const row = head.closest('.gd-pay-row');
    if (!row || row.classList.contains('selected')) return;
    $$('.gd-pay-row').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');
    const method = paymentMethods.find(m => m.key === row.dataset.key);
    if (method) {
        selectedPayFee = method.fee || 0;
        selectedPayLabel = method.title;
        updateSummary();
    }
});

/* ---------- summary ---------- */
function updateSummary() {
    const empty = !selectedPkg;
    $('#sumEmpty').style.display = empty ? '' : 'none';
    $('#sumDetails').style.display = empty ? 'none' : '';
    if (empty) { $('#sumPkgName').textContent = '-'; return; }
    const subtotal = selectedPkg.price * qty;
    const total = Math.max(0, subtotal + selectedPayFee - promoDiscount);
    $('#sumPkgName').textContent = selectedPkg.label;
    $('#sumHarga').textContent = rupiah(selectedPkg.price);
    $('#sumQty').textContent = qty;
    $('#sumFee').textContent = rupiah(selectedPayFee);
    $('#sumTotal').textContent = rupiah(total);
}
updateSummary();

$('#orderNowBtn').addEventListener('click', async function() {
    if (!isAuth) { window.location.href = @json(route('login')); return; }
    if (!selectedPkg) {
      showToast('Pilih produk terlebih dahulu', false);
      return;
    }
    const okUserId = validateField(userIdInput, $('#userIdError'), v => v.length > 0);
    const okZone = validateField(zoneIdInput, $('#zoneIdError'), v => v.length > 0);
    const okEmail = validateField(emailInput, $('#emailError'), v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v));
    const okWa = validateField(waInput, $('#waError'), v => v.length >= 8);
    if (!okUserId || !okZone) {
      userIdInput.scrollIntoView({behavior:'smooth', block:'center'});
      showToast('Lengkapi Data Akun terlebih dahulu', false);
      return;
    }
    if (!okEmail || !okWa) {
      showToast('Periksa kembali Detail Kontak kamu', false);
      return;
    }
    const btn = this;
    btn.disabled = true; btn.textContent = 'Memproses...';
    const fd = new FormData();
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    fd.append('product_id', selectedPkg.id);
    fd.append('customer_number', userIdInput.value.trim());
    fd.append('customer_name', zoneIdInput.value.trim());
    fd.append('quantity', qty);
    fd.append('promo_code', promoInput.value.trim());
    fd.append('email', emailInput.value.trim());
    fd.append('phone', waInput.value.trim());
    try {
      const res = await fetch('{{ route('orders.store') }}', {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}, body:fd});
      if (res.status === 401) { window.location.href = @json(route('login')); return; }
      if (!res.ok) { showToast('Server error: ' + res.status, false); btn.disabled = false; btn.textContent = 'Pesan Sekarang'; return; }
      const data = await res.json();
      if (data.success && data.redirect) { window.location.href = data.redirect; }
      else { btn.disabled = false; btn.textContent = 'Pesan Sekarang'; showToast(data.message || 'Gagal memproses pesanan', false); }
    } catch(e) { btn.disabled = false; btn.textContent = 'Pesan Sekarang'; showToast('Terjadi kesalahan: ' + e.message, false); }
});

$('#adminHelpLink')?.addEventListener('click', e => {
    e.preventDefault();
    showToast('Menghubungkan ke admin support…');
});

/* ---------- promo ---------- */
const promoInput = $('#promoInput'), promoMsg = $('#promoMsg');
$('#promoBtn').addEventListener('click', () => {
    if (!selectedPkg) { showToast('Pilih produk terlebih dahulu', false); return; }
    const code = promoInput.value.trim().toUpperCase();
    if (!code) {
        promoMsg.textContent = 'Masukan kode promo terlebih dahulu.';
        promoMsg.className = 'gd-promo-msg show bad';
        return;
    }
    if (code === 'JOHENI10' || code === 'JOHENGAMING10') {
        promoDiscount = Math.round(selectedPkg.price * qty * 0.10);
        promoMsg.textContent = 'Kode berhasil diterapkan! Diskon ' + rupiah(promoDiscount) + '.';
        promoMsg.className = 'gd-promo-msg show ok';
    } else {
        promoDiscount = 0;
        promoMsg.textContent = 'Kode promo tidak valid atau sudah kedaluwarsa.';
        promoMsg.className = 'gd-promo-msg show bad';
    }
    updateSummary();
});

/* ---------- validation + modal ---------- */
const emailInput = $('#emailInput'), waInput = $('#waInput');

function validateField(input, errorEl, testFn) {
    const ok = testFn(input.value.trim());
    input.classList.toggle('error', !ok);
    errorEl.classList.toggle('show', !ok);
    return ok;
}



/* ---------- toast ---------- */
function showToast(msg, ok) {
    const el = document.createElement('div');
    el.className = 'toast' + (ok === false ? ' error' : ' success') + ' show';
    el.textContent = msg;
    document.body.appendChild(el);
    setTimeout(() => {
        el.classList.remove('show');
        setTimeout(() => el.remove(), 300);
    }, 2600);
}

/* live-clear errors */
[userIdInput, zoneIdInput, emailInput, waInput].forEach(inp => {
    inp.addEventListener('input', () => inp.classList.remove('error'));
});

/* theme-aware payment images */
function swapPayImages() {
    const isLight = document.documentElement.getAttribute('data-theme') === 'light';
    document.querySelectorAll('.gd-pay-icon img[data-light], .gd-pay-cat-logo img[data-light]').forEach(img => {
        if (isLight) {
            img.dataset.dark = img.src;
            img.src = img.dataset.light;
        } else if (img.dataset.dark) {
            img.src = img.dataset.dark;
        }
    });
}
swapPayImages();
document.addEventListener('themeChanged', swapPayImages);

})();
</script>
@endpush
