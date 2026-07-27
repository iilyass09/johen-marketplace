@extends('layouts.topup')

@section('title', 'Jual Beli Akun - Johen Gaming')

@section('content')
@php
  $jbaBanner  = \App\Models\SiteSetting::get('jba_hero_banner');
  $jbaBanner2 = \App\Models\SiteSetting::get('jba_hero_banner_2');
  $jbaBanner3 = \App\Models\SiteSetting::get('jba_hero_banner_3');
  $jbaBanners = array_filter([$jbaBanner, $jbaBanner2, $jbaBanner3]);
@endphp

<div class="jba-page">
  <section class="hero-section" id="jba-hero" style="position:relative;overflow:hidden;border-radius:20px;display:flex;align-items:center;justify-content:center;background:var(--bg-soft)">
    @if(count($jbaBanners))
      <div class="hero-banner-track">
        <img src="{{ asset('storage/'.$jbaBanners[0]) }}" alt="Hero Banner"
             data-banners='{{ json_encode(array_map(fn($b) => asset('storage/'.$b), $jbaBanners)) }}'
             class="hero-banner-img hero-banner-img-a"
             style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center">
        <img src="" alt="Hero Banner"
             class="hero-banner-img hero-banner-img-b"
             style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center">
      </div>
    @else
      <div style="position:absolute;inset:0;background:var(--bg-soft)"></div>
      <div style="position:relative;z-index:1;text-align:center;padding:2rem">
        <p style="color:var(--text-mute);font-size:.82rem">Tambahkan banner di Pengaturan → Hero Banner (Jual Beli Akun)</p>
      </div>
    @endif

    <button class="hero-arrow hero-arrow-left" data-banner-prev aria-label="Sebelumnya">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
    <button class="hero-arrow hero-arrow-right" data-banner-next aria-label="Selanjutnya">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
  </section>

  <div class="jba-transfer-info">
    <div class="jba-transfer-icon">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M12 9v6"/><path d="M9 12h6"/></svg>
    </div>
    <div class="jba-transfer-text">
      <strong>Informasi Transfer</strong>
      <span>Pembayaran hanya melalui transfer ke rekening <strong>BCA 1234567890 a/n Johen Gaming</strong>. Kami tidak pernah meminta transfer ke rekening lain.</span>
    </div>
  </div>

  <div class="jba-hero">
    <h1>Jual Beli Akun Game</h1>
    <p>Temukan akun game terbaik dengan harga terbaik. Semua akun sudah diverifikasi.</p>
  </div>

  <div class="games-grid" id="jba-game-grid">
    @foreach($popularGames as $i => $brand)
      @php
        $count = $listings->get($brand->name)?->count() ?? 0;
      @endphp
      <div class="jba-game-btn" data-game="{{ $brand->name }}">
        <div class="jba-game-btn-icon">
          @if($brand->thumbnail_url)
            <img src="{{ $brand->thumbnail_url }}" alt="{{ $brand->name }}">
          @else
            <span style="font-size:2.4rem">🎮</span>
          @endif
        </div>
        <div class="jba-game-btn-overlay"></div>
        <div class="jba-game-btn-info">
          <div class="jba-game-btn-name">{{ $brand->name }}</div>
          <div class="jba-game-btn-cat">{{ $count }} akun tersedia</div>
        </div>
      </div>
    @endforeach
  </div>

  <div id="jba-game-section" class="jba-game-section" style="display:none">
    <button class="jba-back-btn" id="jbaBackBtn">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Kembali
    </button>

<div class="jba-game-header">
  <h2 class="jba-game-title" id="jbaGameTitle"></h2>
</div>

<div class="jba-sort-bar">
  <span class="jba-sort-label">Urutkan</span>
  <div class="jba-sort-options">
    <button class="jba-sort-btn active" data-sort="default">Default</button>
    <button class="jba-sort-btn" data-sort="price-asc">Termurah</button>
    <button class="jba-sort-btn" data-sort="price-desc">Termahal</button>
  </div>
</div>

<div class="jba-grid" id="jbaGrid"></div>
  </div>
</div>

<style>
:root {
  --jba-accent: #9d5cf5;
}
.jba-page {
  max-width: 1400px;
  margin: 0 auto;
  padding: 1.5rem 2rem 1.5rem;
}
.jba-transfer-info {
  display: flex;
  align-items: center;
  gap: .75rem;
  padding: .85rem 1rem;
  margin: .75rem 0 1rem;
  border-radius: 12px;
  background: rgba(157, 92, 245, .08);
  border: 1px solid rgba(157, 92, 245, .25);
  animation: jbaGlowPulse 2s ease-in-out infinite;
}
@keyframes jbaGlowPulse {
  0%, 100% { border-color: rgba(157, 92, 245, .25); box-shadow: 0 0 6px rgba(157, 92, 245, .08); }
  50% { border-color: rgba(157, 92, 245, .7); box-shadow: 0 0 14px rgba(157, 92, 245, .25); }
}
.jba-transfer-icon {
  flex-shrink: 0;
  width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: rgba(157, 92, 245, .15);
  color: var(--jba-accent);
}
.jba-transfer-text {
  font-size: .8rem;
  color: var(--text-dim);
  line-height: 1.5;
}
.jba-transfer-text strong {
  color: var(--text);
}
.jba-hero {
  text-align: left;
  margin: 1.2rem 0 1.5rem;
}
.jba-hero h1 {
  font-size: 1.3rem;
  font-weight: 700;
  margin-bottom: .15rem;
  color: var(--text);
}
.jba-hero p {
  color: var(--text-dim);
  font-size: .82rem;
  margin: 0;
}

.jba-game-btn {
  border-radius: var(--radius-md);
  overflow: hidden;
  position: relative;
  aspect-ratio: 3/4;
  cursor: pointer;
  transition: transform .25s ease, box-shadow .25s ease;
  display: flex;
  align-items: flex-end;
  background: var(--surface-2);
  border: 1px solid var(--border);
}
.jba-game-btn:hover {
  transform: translateY(-6px) scale(1.02);
  box-shadow: 0 20px 40px -14px rgba(0,0,0,.65);
  border-color: var(--purple-light);
}
.jba-game-btn-icon {
  position: absolute; inset: 0;
  overflow: hidden;
}
.jba-game-btn-icon img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform .3s ease;
}
.jba-game-btn:hover .jba-game-btn-icon img {
  transform: scale(1.08);
}
.jba-game-btn-icon span {
  position: absolute; inset: 0;
  display: flex; align-items: center; justify-content: center;
  font-size: 3.6rem;
}
.jba-game-btn-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(180deg, transparent 35%, rgba(0,0,0,.85) 80%);
  pointer-events: none;
}
.jba-game-btn-info {
  position: relative; z-index: 2;
  padding: .8rem;
  width: 100%;
}
.jba-game-btn-name {
  font-family: var(--font-display);
  font-weight: 700;
  font-size: .88rem;
  line-height: 1.15;
  color: #fff;
}
.jba-game-btn-cat {
  font-size: .66rem;
  color: rgba(255,255,255,.7);
  text-transform: uppercase;
  letter-spacing: .04em;
  margin-top: .15rem;
}

/* Game section */
.jba-game-section {
  animation: jbaFadeIn .25s ease;
}
@keyframes jbaFadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}
.jba-back-btn {
  display: inline-flex;
  align-items: center;
  gap: .35rem;
  padding: .4rem .75rem;
  border-radius: 8px;
  font-size: .82rem;
  font-weight: 500;
  background: var(--surface-2);
  border: 1px solid var(--glass-border);
  color: var(--text-dim);
  cursor: pointer;
  transition: all .18s ease;
  margin-bottom: 1rem;
  font-family: inherit;
}
.jba-back-btn:hover {
  border-color: var(--jba-accent);
  color: var(--text);
}
.jba-game-header {
  margin-bottom: 1rem;
}
.jba-game-title {
  font-size: 1.3rem;
  font-weight: 700;
  padding-left: .5rem;
  border-left: 3px solid var(--jba-accent);
}
.jba-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 1rem;
}
.jba-card {
  border-radius: 12px;
  overflow: hidden;
  transition: transform .25s ease, box-shadow .25s ease;
  text-decoration: none;
  color: inherit;
  display: flex;
  flex-direction: column;
  background: var(--bg-card);
  border: 1.5px solid rgba(157, 92, 245, .2);
}
.jba-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 40px -14px rgba(0,0,0,.65);
  border-color: var(--jba-accent);
}
.jba-card-img {
  width: 100%;
  aspect-ratio: 4/3;
  overflow: hidden;
  background: var(--bg-soft);
  position: relative;
}
.jba-card-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.jba-card-img-fallback {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-soft);
  color: #555;
}
.jba-ribbon {
  position: absolute;
  top: 14px;
  left: -30px;
  background: linear-gradient(135deg, #E53935 0%, #c62828 100%);
  color: #fff;
  font-size: 0.68rem;
  font-weight: 800;
  padding: 2px 36px;
  z-index: 3;
  text-align: center;
  text-transform: uppercase;
  white-space: nowrap;
  letter-spacing: 0.04em;
  line-height: 1.6;
  transform: rotate(-45deg);
  box-shadow: 0 3px 10px rgba(0,0,0,0.3);
  pointer-events: none;
}
.jba-card:hover .jba-ribbon {
  transform: rotate(-45deg) scale(1.06);
  box-shadow: 0 4px 16px rgba(229, 57, 53, 0.5);
}
.jba-ribbon::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  border: 5px solid #a82121;
  border-left-color: transparent;
  border-bottom-color: transparent;
}
.jba-ribbon::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  border: 5px solid #a82121;
  border-right-color: transparent;
  border-top-color: transparent;
}
.jba-card--sold {
  pointer-events: none;
  cursor: default;
  opacity: .75;
}
.jba-card--sold .jba-card-game,
.jba-card--sold .jba-card-title,
.jba-card--sold .jba-card-owner {
  color: var(--text-dim) !important;
  opacity: .5;
}
.jba-card--sold .jba-card-prices * {
  filter: grayscale(1);
  opacity: .4;
}
.jba-card--sold .jba-card-img::after {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,.5);
  z-index: 1;
}
.jba-img-sold {
  filter: grayscale(1) brightness(.55);
}
.jba-ribbon--sold {
  background: linear-gradient(135deg, #424242 0%, #212121 100%);
}
.jba-ribbon--sold::before {
  border-color: transparent transparent #111 #111;
}
.jba-ribbon--sold::after {
  border-color: #111 #111 transparent transparent;
}
.jba-card-body {
  padding: .85rem 1rem;
}
.jba-card-game {
  font-size: .65rem;
  color: var(--jba-accent);
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: .04em;
  margin-bottom: .15rem;
  opacity: .7;
}
.jba-card-title {
  font-size: .88rem;
  font-weight: 600;
  margin-bottom: .15rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.3;
  color: var(--text);
}
.jba-card-owner {
  font-size: .75rem;
  color: var(--text-dim);
  font-weight: 400;
  margin-bottom: .35rem;
}
.jba-card-prices {
  display: flex;
  align-items: center;
  gap: .5rem;
  flex-wrap: wrap;
}
.jba-card-original {
  font-size: .78rem;
  color: #ef4444;
  text-decoration: line-through;
  opacity: .7;
}
.jba-card-price {
  font-size: .95rem;
  font-weight: 700;
  color: var(--jba-accent);
}
.jba-empty {
  text-align: center;
  padding: 4rem 1rem;
  color: var(--text-dim);
  grid-column: 1 / -1;
  display: flex;
  flex-direction: column;
  align-items: center;
}
@media(max-width:640px){
  .jba-page{padding:1rem 1.1rem 1.5rem;}
  .jba-grid{grid-template-columns:repeat(2,1fr);gap:.7rem;}
  .jba-card-body{padding:.6rem .7rem;}
  .jba-card-title{font-size:.8rem;}
  .jba-card-price{font-size:.85rem;}
  .jba-card-original{font-size:.7rem;}
  .jba-card-game{font-size:.6rem;}
}
.jba-empty h3 {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: .3rem;
  color: var(--text-primary);
}
.jba-empty p {
  font-size: .88rem;
}
.jba-sort-bar {
  display: flex;
  align-items: center;
  gap: .6rem;
  margin-bottom: 1rem;
  padding-left: .5rem;
}
.jba-sort-label {
  font-size: .78rem;
  color: var(--text-dim);
  font-weight: 600;
  white-space: nowrap;
}
.jba-sort-options {
  display: flex;
  gap: .35rem;
  flex-wrap: wrap;
}
.jba-sort-btn {
  padding: .4rem .75rem;
  border-radius: 8px;
  font-size: .75rem;
  font-weight: 600;
  border: 1px solid var(--border);
  background: var(--surface);
  color: var(--text-dim);
  cursor: pointer;
  transition: all .16s ease;
  font-family: inherit;
}
.jba-sort-btn:hover {
  border-color: var(--jba-accent);
  color: var(--text);
}
.jba-sort-btn.active {
  background: var(--jba-accent);
  border-color: var(--jba-accent);
  color: #fff;
}
</style>

<script>
(function(){
  const gameGrid = document.getElementById('jba-game-grid');
  const gameSection = document.getElementById('jba-game-section');
  const gameTitle = document.getElementById('jbaGameTitle');
  const backBtn = document.getElementById('jbaBackBtn');
  const grid = document.getElementById('jbaGrid');

  const allListings = @json($listings);
  let currentGame = null;
  let currentSort = 'default';

  // auto-open game from ?game= param
  const params = new URLSearchParams(location.search);
  const gameName = params.get('game');
  if (gameName && allListings[gameName]) {
    showGame(gameName);
  }

  function sortListings(listings, sort) {
    const arr = [...(listings || [])];
    switch (sort) {
      case 'price-asc': arr.sort((a, b) => Number(a.price) - Number(b.price)); break;
      case 'price-desc': arr.sort((a, b) => Number(b.price) - Number(a.price)); break;
    }
    return arr;
  }

  function renderCards(listings) {
    grid.innerHTML = '';
    let visible = 0;

    (listings || []).forEach(function(l) {
      visible++;

      const isSold = l.is_sold;
      const price = 'Rp ' + Number(l.price).toLocaleString('id-ID');
      const orig = l.original_price ? 'Rp ' + Number(l.original_price).toLocaleString('id-ID') : null;

      let badgeHtml = '';
      if (isSold) {
        badgeHtml = '<div class="jba-ribbon jba-ribbon--sold">SOLD</div>';
      } else if (l.promo_type && l.promo_type !== 'none') {
        let text = '';
        if (l.promo_type === 'diskon' && l.discount_percent) text = '-' + l.discount_percent + '%';
        else if (l.promo_type === 'promo') text = 'Promo';
        else if (l.promo_type === 'flash_sale') text = 'Flash Sale';
        else if (l.promo_type === 'best_seller') text = 'Best Seller';
        else if (l.promo_type === 'hot') text = 'Hot';
        else if (l.promo_type === 'new') text = 'New';
        else if (l.promo_type === 'limited') text = 'Limited';
        badgeHtml = '<div class="jba-ribbon">' + text + '</div>';
      }

      const thumbHtml = l.photo_url
        ? '<img src="' + l.photo_url + '" alt="' + l.product_name + '" class="' + (isSold ? 'jba-img-sold' : '') + '">'
        : '<div class="jba-card-img-fallback"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg></div>';

      const href = isSold ? '#' : '/jual-beli-akun/' + l.id;

      const card = document.createElement('a');
      card.href = href;
      card.className = 'jba-card' + (isSold ? ' jba-card--sold' : '');
      if (isSold) { card.setAttribute('tabindex', '-1'); card.setAttribute('aria-disabled', 'true'); }
      card.innerHTML = '<div class="jba-card-img">' + thumbHtml + badgeHtml + '</div>' +
        '<div class="jba-card-body">' +
        '<div class="jba-card-game">' + l.game + '</div>' +
        '<h3 class="jba-card-title">' + l.product_name + '</h3>' +
        (l.owner_name ? '<div class="jba-card-owner">' + l.owner_name + '</div>' : '') +
        '<div class="jba-card-prices">' +
        (orig ? '<span class="jba-card-original">' + orig + '</span>' : '') +
        '<span class="jba-card-price">' + price + '</span>' +
        '</div></div>';
      grid.appendChild(card);
    });

    if (visible === 0) {
      grid.innerHTML = '<div class="jba-empty"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="color:#555;margin-bottom:.75rem"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg><h3>Tidak Ada Akun</h3><p>Tidak ada akun untuk filter ini.</p></div>';
    }
  }

  const jbaHero = document.querySelector('.jba-hero');
  const jbaBanner = document.getElementById('jba-hero');

  function showGame(game) {
    currentGame = game;
    gameGrid.style.display = 'none';
    gameSection.style.display = '';
    if (jbaHero) jbaHero.style.display = 'none';
    if (jbaBanner) jbaBanner.style.display = 'none';
    gameTitle.textContent = game;
    renderCards(sortListings(allListings[game] || [], currentSort));
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  /* ---------- sort ---------- */
  document.querySelectorAll('.jba-sort-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.jba-sort-btn').forEach(function(b) { b.classList.remove('active'); });
      btn.classList.add('active');
      currentSort = btn.dataset.sort;
      if (currentGame) {
        renderCards(sortListings(allListings[currentGame] || [], currentSort));
      }
    });
  });

  document.querySelectorAll('.jba-game-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      showGame(this.dataset.game);
    });
  });

  backBtn?.addEventListener('click', function() {
    gameSection.style.display = 'none';
    gameGrid.style.display = '';
    if (jbaHero) jbaHero.style.display = '';
    if (jbaBanner) jbaBanner.style.display = '';
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
})();
</script>

<!-- ===== TESTIMONIALS ===== -->
<section class="testi-section">
  <h2>APA KATA MEREKA?</h2>
  <p class="testi-sub">Ribuan orang telah mempercayai Transaksi mereka di Johen Gaming</p>
  <div class="testi-carousel">
    <button class="testi-arrow testi-arrow-left" onclick="prevTestiJba()" aria-label="Sebelumnya">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
    <div class="testi-track" id="testiTrackJba"></div>
    <button class="testi-arrow testi-arrow-right" onclick="nextTestiJba()" aria-label="Selanjutnya">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
  </div>
  <div class="testi-dots" id="testiDotsJba"></div>
  <div class="load-more-wrap" style="margin-top:1.2rem">
    <a href="{{ route('testimoni', ['layanan' => 'jual-beli-akun']) }}" class="btn btn-outline btn-load-more">Lihat Selengkapnya</a>
  </div>
</section>

<script>
// ============ TESTIMONIALS CAROUSEL (JBA) ============
const testiTrackJba = document.getElementById('testiTrackJba');
const testiDotsJba = document.getElementById('testiDotsJba');
let testiJbaCurrent = 0;
let testiJbaTimer = null;

function createTestiCardJba(t) {
  const card = document.createElement('div');
  card.className = 'testi-card';
  card.innerHTML = `
    <div class="testi-user">
      <div class="testi-avatar">${t.avatar}</div>
      <div>
        <div class="testi-name">${t.name}</div>
        <div class="testi-game">${t.game}</div>
      </div>
    </div>
    <p class="testi-quote">"${t.quote}"</p>`;
  return card;
}

function getTestiOffsetJba(i) {
  const cw = testiTrackJba.parentElement.getBoundingClientRect().width;
  const card = testiTrackJba.children[i + 1];
  return card.offsetLeft - (cw - card.offsetWidth) / 2;
}

function applyTestiCenterJba(i) {
  testiTrackJba.querySelectorAll('.testi-card').forEach((c, idx) => c.classList.toggle('center', idx === i + 1));
  if (testiDotsJba) Array.from(testiDotsJba.children).forEach((d, idx) => d.classList.toggle('active', idx === i));
}

function goTestiJba(i) {
  testiJbaCurrent = i;
  if (i > testiJbaTotal - 1) { testiJbaCurrent = 0; }
  if (i < 0) { testiJbaCurrent = testiJbaTotal - 1; }
  testiTrackJba.style.transform = 'translateX(' + (-getTestiOffsetJba(testiJbaCurrent)) + 'px)';
  applyTestiCenterJba(testiJbaCurrent);
}

function prevTestiJba() { goTestiJba(testiJbaCurrent - 1); resetTestiJbaTimer(); }
function nextTestiJba() { goTestiJba(testiJbaCurrent + 1); resetTestiJbaTimer(); }
function resetTestiJbaTimer() {
  if (testiJbaTimer) clearInterval(testiJbaTimer);
  testiJbaTimer = setInterval(function() { goTestiJba(testiJbaCurrent + 1); }, 5000);
}

const testiJbaData = @json($testimonials);
const testiJbaTotal = testiJbaData.length;

if (testiTrackJba && testiJbaTotal > 0) {
  testiJbaData.forEach(t => testiTrackJba.appendChild(createTestiCardJba(t)));
  const clones = Array.from(testiTrackJba.children);
  testiTrackJba.appendChild(clones[0].cloneNode(true));
  testiTrackJba.insertBefore(clones[testiJbaTotal - 1].cloneNode(true), testiTrackJba.firstChild);
  testiTrackJba.style.transition = 'none';
  testiTrackJba.style.transform = 'translateX(' + (-getTestiOffsetJba(0)) + 'px)';
  void testiTrackJba.offsetHeight;
  testiTrackJba.style.transition = '';
  applyTestiCenterJba(0);
  if (testiDotsJba) {
    testiJbaData.forEach(function(_, i) {
      const dot = document.createElement('button');
      dot.className = 'testi-dot' + (i === 0 ? ' active' : '');
      dot.setAttribute('aria-label', 'Testimonial ' + (i + 1));
      dot.addEventListener('click', function() { goTestiJba(i); resetTestiJbaTimer(); });
      testiDotsJba.appendChild(dot);
    });
  }
  testiJbaTimer = setInterval(function() { goTestiJba(testiJbaCurrent + 1); }, 5000);
}
</script>

<!-- ===== PAYMENT METHODS ===== -->
<section class="payment-section">
  <h2>METODE PEMBAYARAN</h2>
  <p>Kami mendukung berbagai metode pembayaran seperti QRIS, e-wallet, virtual account dan minimarket.</p>
  <div class="payment-track-wrap">
    <div class="payment-track" id="paymentTrack"></div>
  </div>
</section>

<!-- ===== CTA ===== -->
<section class="cta-section">
  <div class="cta-card">
    <span class="cta-glow-2"></span>
    <a href="https://www.johengaming.id" target="_blank" rel="noopener noreferrer" class="cta-logo-link">
      <img src="{{ asset('logo.png') }}" alt="Johen Gaming" class="cta-logo">
    </a>
    <h2>Kunjungi Website Profile Kami</h2>
    <p>Dapatkan informasi lengkap tentang layanan, promo terbaru, dan update seputar Johen Gaming.</p>
    <a href="https://www.johengaming.id" target="_blank" rel="noopener noreferrer" class="cta-btn">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
      Kunjungi johengaming.id
    </a>
  </div>
</section>
@endsection