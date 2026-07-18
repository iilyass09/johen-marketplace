@extends('layouts.topup')

@section('title', 'Jual Beli Akun - Johen Gaming')

@section('content')
<div class="jba-page">
  <div class="jba-hero">
    <h1>Jual Beli Akun Game</h1>
    <p>Temukan akun game terbaik dengan harga terbaik. Semua akun sudah diverifikasi.</p>
  </div>

  <div class="games-grid" id="jba-game-grid">
    @php
      $jbaGradients = [
        'linear-gradient(160deg,#1e3a5f,#0f1c2e)',
        'linear-gradient(160deg,#3b2465,#1a1030)',
        'linear-gradient(160deg,#5c1f2e,#240d13)',
        'linear-gradient(160deg,#1f4d2e,#0d1f13)',
        'linear-gradient(160deg,#4a1f5c,#1a0d24)',
      ];
    @endphp
    @foreach($popularGames as $i => $brand)
      @php
        $count = $listings->get($brand->name)?->count() ?? 0;
        $bg = $jbaGradients[$i % count($jbaGradients)];
      @endphp
      <div class="jba-game-btn" data-game="{{ $brand->name }}" style="background:{{ $bg }}">
        <div class="jba-game-btn-icon">
          @if($brand->thumbnail_url)
            <img src="{{ $brand->thumbnail_url }}" alt="{{ $brand->name }}" style="width:100%;height:100%;object-fit:contain;padding:1.5rem;">
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

    <div class="jba-grid" id="jbaGrid"></div>
  </div>
</div>

<style>
:root {
  --jba-accent: #9d5cf5;
}
.jba-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1.5rem 4rem;
}
.jba-hero {
  text-align: center;
  margin-bottom: 2.5rem;
}
.jba-hero h1 {
  font-size: 1.8rem;
  font-weight: 800;
  margin-bottom: .4rem;
  color: var(--text);
}
.jba-hero p {
  color: var(--text-dim);
  font-size: .92rem;
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
}
.jba-game-btn:hover {
  transform: translateY(-6px) scale(1.02);
  box-shadow: 0 20px 40px -14px rgba(0,0,0,.65);
}
.jba-game-btn-icon {
  position: absolute; inset: 0;
  display: flex; align-items: center; justify-content: center;
  font-size: 3.6rem;
  filter: drop-shadow(0 8px 14px rgba(0,0,0,.4));
  transition: transform .3s ease;
}
.jba-game-btn:hover .jba-game-btn-icon {
  transform: scale(1.1) translateY(-4px);
}
.jba-game-btn-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(180deg, transparent 40%, rgba(0,0,0,.85));
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
}
.jba-game-btn-cat {
  font-size: .66rem;
  color: var(--text-dim);
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
  background: var(--bg-card);
  border: 1.5px solid rgba(157, 92, 245, .25);
  border-radius: 10px;
  overflow: hidden;
  transition: transform .2s, box-shadow .2s, border-color .2s;
  text-decoration: none;
  color: inherit;
}
.jba-card:hover {
  transform: translateY(-3px);
  border-color: var(--jba-accent);
  box-shadow: 0 0 20px -4px rgba(157, 92, 245, .5), 0 8px 24px -6px rgba(0,0,0,.35);
}
.jba-card-thumb {
  width: 100%;
  aspect-ratio: 4/3;
  overflow: hidden;
  background: var(--bg-soft);
  position: relative;
}
.jba-card-thumb::before {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,.2);
  z-index: 1;
  pointer-events: none;
}
.jba-card-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.jba-card-thumb-fallback {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
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
  z-index: 2;
  text-align: center;
  text-transform: uppercase;
  white-space: nowrap;
  letter-spacing: 0.04em;
  line-height: 1.6;
  transform: rotate(-45deg);
  box-shadow: 0 3px 10px rgba(0,0,0,0.3);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
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
  position: relative;
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
.jba-card--sold .jba-card-thumb::after {
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
  font-size: .92rem;
  font-weight: 600;
  margin-bottom: .2rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.jba-card-owner {
  font-size: .78rem;
  color: var(--text-dim);
  font-weight: 400;
  margin-bottom: .25rem;
}
.jba-card-prices {
  display: flex;
  align-items: center;
  gap: .5rem;
  flex-wrap: wrap;
}
.jba-card-original {
  font-size: .82rem;
  color: #ef4444;
  text-decoration: line-through;
  opacity: .7;
}
.jba-card-price {
  font-size: 1rem;
  font-weight: 700;
  color: var(--jba-accent);
}
.jba-empty {
  text-align: center;
  padding: 4rem 1rem;
  color: var(--text-dim);
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
</style>

<script>
(function(){
  const gameGrid = document.getElementById('jba-game-grid');
  const gameSection = document.getElementById('jba-game-section');
  const gameTitle = document.getElementById('jbaGameTitle');
  const backBtn = document.getElementById('jbaBackBtn');
  const grid = document.getElementById('jbaGrid');

  const allListings = @json($listings);

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
        : '<div class="jba-card-thumb-fallback"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg></div>';

      const href = isSold ? '#' : '/jual-beli-akun/' + l.id;

      const card = document.createElement('a');
      card.href = href;
      card.className = 'jba-card' + (isSold ? ' jba-card--sold' : '');
      if (isSold) { card.setAttribute('tabindex', '-1'); card.setAttribute('aria-disabled', 'true'); }
      card.innerHTML = '<div class="jba-card-thumb">' + thumbHtml + badgeHtml + '</div>' +
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

  function showGame(game) {
    gameGrid.style.display = 'none';
    gameSection.style.display = '';
    gameTitle.textContent = game;
    renderCards(allListings[game] || []);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  document.querySelectorAll('.jba-game-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      showGame(this.dataset.game);
    });
  });

  backBtn?.addEventListener('click', function() {
    gameSection.style.display = 'none';
    gameGrid.style.display = '';
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
})();
</script>
@endsection