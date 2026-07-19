@extends('layouts.topup')

@section('content')
@php
  $banner = \App\Models\SiteSetting::get('site_hero_banner');
@endphp

<!-- ===== HERO BANNER ===== -->
<section class="hero-section" id="joki" style="position:relative;overflow:hidden;height:500px;border-radius:20px;display:flex;align-items:center;justify-content:center;background:var(--bg-soft)">
  @if($banner)
    <img src="{{ asset('storage/'.$banner) }}" alt="Hero Banner"
         style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center">
  @else
    <div style="position:absolute;inset:0;background:var(--bg-soft)"></div>
    <div style="position:relative;z-index:1;text-align:center;padding:2rem">
      <h1 style="font-size:2rem;font-weight:800;margin-bottom:.5rem">JOHEN GAMING</h1>
      <p style="color:var(--text-dim);font-size:1rem">Top Up Game Termurah & Terpercaya</p>
      <p style="color:var(--text-mute);font-size:.82rem;margin-top:1rem">Tambahkan banner di Pengaturan → Hero Banner</p>
    </div>
  @endif
</section>

<!-- ===== CATEGORY TABS ===== -->
<section class="tabs-section">
  <div class="tabs-wrap" id="tabsWrap">
    <button class="tab-pill active" data-filter="all">Top Up Games</button>
    <button class="tab-pill" data-filter="joki" id="jokiTab">Joki Mobile Legends</button>
    <button class="tab-pill" data-filter="check" id="cekTransaksiTab">Cek Transaksi</button>
  </div>
</section>

<!-- ===== SECTION HEADER ===== -->
<section class="section-heading">
  <h2>PRODUK UNGGULAN</h2>
  <p>Pilihan terbaik dengan transaksi terbanyak dan rating tertinggi dari pengguna.</p>
</section>

<!-- ===== FEATURED GAMES ===== -->
@php
  $featuredGameNames = ['PUBG Mobile', 'Mobile Legends', 'Free Fire', 'Roblox', 'Valorant'];
  $featuredBrands = \App\Models\Brand::whereIn('name', $featuredGameNames)->get()->keyBy('name');
  $featuredFallback = [
    'PUBG Mobile' => ['icon' => '🔫', 'publisher' => 'Tencent', 'desc' => 'Top Up UC PUBG Mobile cepat & instan.', 'rating' => '4.9', 'sales' => '1.2M+', 'time' => '<1 Menit', 'price' => 'Rp1.500'],
    'Mobile Legends' => ['icon' => '🎮', 'publisher' => 'Moonton', 'desc' => 'Top Up diamond Mobile Legends cepat & instan.', 'rating' => '4.8', 'sales' => '950K+'],
    'Free Fire' => ['icon' => '🔥', 'publisher' => 'Garena', 'desc' => 'Dapatkan diamond Free Fire harga spesial.', 'rating' => '4.7', 'sales' => '780K+'],
    'Roblox' => ['icon' => '🧱', 'publisher' => 'Roblox Corp', 'desc' => 'Beli Robux dengan harga terbaik.', 'rating' => '4.8', 'sales' => '650K+'],
    'Valorant' => ['icon' => '🔫', 'publisher' => 'Riot Games', 'desc' => 'Top Up VALORANT Points murah.', 'rating' => '4.6', 'sales' => '520K+'],
  ];
  function fgData($name, $brands, $fallback) {
    $b = $brands->get($name);
    return (object)[
      'name' => $b->name ?? $name,
      'icon' => $b->icon ?? $fallback[$name]['icon'],
      'publisher' => $b->category ?? $fallback[$name]['publisher'],
      'desc' => $fallback[$name]['desc'],
      'thumb' => $b->featured_thumbnail_url ?? null,
      'imgs' => $b ? $b->featured_img_urls : [],
      'rating' => $fallback[$name]['rating'],
      'sales' => $fallback[$name]['sales'],
      'time' => $fallback[$name]['time'] ?? '<1 Menit',
      'price' => $fallback[$name]['price'] ?? 'Rp1.500',
    ];
  }
  $pubg = fgData('PUBG Mobile', $featuredBrands, $featuredFallback);
  $ml   = fgData('Mobile Legends', $featuredBrands, $featuredFallback);
  $ff   = fgData('Free Fire', $featuredBrands, $featuredFallback);
  $rbx  = fgData('Roblox', $featuredBrands, $featuredFallback);
  $valo = fgData('Valorant', $featuredBrands, $featuredFallback);
  $colors = [
    ['primary' => '#f59e0b', 'secondary' => '#2a1f0a'],
    ['primary' => '#7c3aed', 'secondary' => '#1e0f3a'],
    ['primary' => '#ef4444', 'secondary' => '#2a0f0f'],
    ['primary' => '#10b981', 'secondary' => '#0a1f15'],
    ['primary' => '#ff4655', 'secondary' => '#1a0a0f'],
  ];
  function fgBgStyle($game, $color) {
    $img = $game->imgs[0] ?? $game->thumb;
    if ($img) return "background-image:url('{$img}');background-size:cover;background-position:center";
    return "background:linear-gradient(160deg,{$color['primary']},{$color['secondary']})";
  }
  $rightCards = [$ml, $ff, $rbx, $valo];
  $rightBadges = ['🔥 Populer', '🚀 Trending', '⭐ Favorit', '💎 Premium'];
  $rightColors = [$colors[1], $colors[2], $colors[3], $colors[4]];
@endphp
<section class="featured-grid-section" id="topup">
  <div class="featured-grid-inner">
    <a href="{{ route('games.show', $pubg->name) }}" class="bento-card bento-featured"
       data-featured-imgs='{{ json_encode($pubg->imgs) }}'>
      <div class="bento-card-bg" style="{{ fgBgStyle($pubg, $colors[0]) }}"></div>
      <div class="bento-overlay"></div>
      <div class="bento-featured-content">
        <span class="bento-badge bento-badge-best">🔥 Best Seller</span>
        <h3 class="bento-featured-title">{{ $pubg->name }}</h3>
        <p class="bento-featured-sub">{{ $pubg->desc }}</p>
        <div class="bento-stats">
          <span class="bento-stat">⭐ {{ $pubg->rating }}</span>
          <span class="bento-stat">👥 {{ $pubg->sales }}</span>
          <span class="bento-stat">⚡ {{ $pubg->time }}</span>
        </div>
        <div class="bento-price">
          <span class="bento-price-label">Mulai dari</span>
          <span class="bento-price-value">{{ $pubg->price }}</span>
        </div>
        <span class="bento-cta">⚡ Top Up Sekarang</span>
      </div>
    </a>

    <div class="bento-grid-right">
      @foreach($rightCards as $i => $card)
      <a href="{{ route('games.show', $card->name) }}" class="bento-card bento-small-card"
         data-featured-imgs='{{ json_encode($card->imgs) }}'>
        <div class="bento-card-bg" style="{{ fgBgStyle($card, $rightColors[$i]) }}"></div>
        <div class="bento-overlay"></div>
        <div class="bento-small-content">
          <span class="bento-small-badge">{{ $rightBadges[$i] }}</span>
          <h4 class="bento-small-title">{{ $card->name }}</h4>
          <div class="bento-small-meta">
            <span>⭐ {{ $card->rating }}</span>
            <span>👥 {{ $card->sales }}</span>
          </div>
          <span class="bento-small-cta">Top Up →</span>
        </div>
      </a>
      @endforeach
    </div>
  </div>
</section>

<!-- ===== SECTION HEADER PRODUK LAINNYA ===== -->
<section class="section-heading">
  <h2>PRODUK LAINNYA</h2>
  <p>Jelajahi game populer lainnya yang tersedia di Johen Gaming.</p>
</section>

<!-- ===== GAMES GRID ===== -->
<section class="games-section" id="games">
  @php
    $gradients = [
      'linear-gradient(160deg,#1e3a5f,#0f1c2e)',
      'linear-gradient(160deg,#3b2465,#1a1030)',
      'linear-gradient(160deg,#5c1f2e,#240d13)',
      'linear-gradient(160deg,#1f4d2e,#0d1f13)',
      'linear-gradient(160deg,#4a1f5c,#1a0d24)',
    ];
  @endphp

  <div class="games-grid" id="gamesGrid">
    @foreach($brands as $i => $brand)
      @php
        $icon = $brand->icon ?? '🎮';
        $bg = $gradients[$i % count($gradients)];
        $hidden = $i >= 10 ? 'style=display:none' : '';
      @endphp
      <a href="{{ route('games.show', $brand->name) }}" class="game-card"
         data-brand="{{ $brand->name }}"
         data-category="{{ $brand->category ?? 'other' }}"
         data-service-type="{{ $brand->service_type ?? 'topup' }}"
         data-icon="{{ $icon }}"
         data-thumbnail="{{ $brand->thumbnail_url ?? '' }}"
         data-index="{{ $i }}"
         {!! $hidden !!}
         style="background:{{ $bg }};animation:cardIn .5s ease forwards;animation-delay:{{ $i * 0.04 }}s;opacity:0;transform:translateY(16px);{{ $i >= 10 ? 'display:none;' : '' }}">
        <div class="game-card-icon">
          @if($brand->thumbnail_url)
            <img src="{{ $brand->thumbnail_url }}" alt="{{ $brand->name }}" style="width:100%;height:100%;object-fit:cover;">
          @else
            {{ $icon }}
          @endif
        </div>
        <div class="game-card-overlay"></div>
        <div class="game-card-info">
          <div class="game-card-name">{{ $brand->name }}</div>
          <div class="game-card-cat">{{ $brand->category ?? 'other' }}</div>
        </div>
      </a>
    @endforeach
  </div>

  <div class="load-more-wrap" id="loadMoreWrap">
    <button class="btn btn-outline btn-load-more" id="loadMoreBtn">Tampilkan Lainnya</button>
  </div>
</section>

<style>
@keyframes cardIn{to{opacity:1;transform:translateY(0);}}
</style>

@push('scripts')
<script>
let loadMoreIndex = 10;
const step = 5;
const allCards = Array.from(document.querySelectorAll('.game-card'));
const loadMoreBtn = document.getElementById('loadMoreBtn');
const loadMoreWrap = document.getElementById('loadMoreWrap');

if (loadMoreBtn) {
  loadMoreBtn.addEventListener('click', function() {
    const currentFilter = document.querySelector('.tab-btn.active')?.dataset?.tab || 'all';
    let shown = 0;
    let hidden = [];

    allCards.forEach(card => {
      if (currentFilter === 'all' || card.dataset.brand === 'Mobile Legends' && currentFilter === 'joki') {
        if (card.style.display !== 'none') {
          shown++;
        } else if (parseInt(card.dataset.index) >= loadMoreIndex) {
          hidden.push(card);
        }
      }
    });

    const toShow = hidden.slice(0, step);
    toShow.forEach(card => { card.style.display = ''; });
    loadMoreIndex += toShow.length;

    const totalInFilter = allCards.filter(c => {
      if (currentFilter === 'all') return true;
      if (currentFilter === 'joki') return c.dataset.brand === 'Mobile Legends';
      return c.dataset.brand === currentFilter;
    }).length;

    const visibleCount = allCards.filter(c => c.style.display !== 'none' && c.style.display !== 'display:none').length;

    if (visibleCount >= totalInFilter) {
      loadMoreWrap.style.display = 'none';
    }
  });
}

window.__loadMoreReset = function() {
  allCards.forEach((card, i) => {
    if (i >= 10) {
      card.style.display = 'none';
    } else {
      card.style.display = '';
    }
  });
  loadMoreIndex = 10;
  loadMoreWrap.style.display = '';
};
</script>
@endpush

<!-- ===== TESTIMONIALS ===== -->
<section class="testi-section">
  <h2>APA KATA MEREKA?</h2>
  <p class="testi-sub">Ribuan orang telah mempercayai Top Up mereka di Johen Gaming</p>
  <div class="testi-carousel">
    <div class="testi-track" id="testiTrack"></div>
  </div>
  <div class="testi-dots" id="testiDots"></div>
</section>

<!-- ===== PAYMENT METHODS ===== -->
<section class="payment-section">
  <h2>METODE PEMBAYARAN</h2>
  <p>Kami mendukung berbagai metode pembayaran seperti QRIS, e-wallet, virtual account dan minimarket.</p>
  <div class="payment-track-wrap">
    <div class="payment-track" id="paymentTrack"></div>
  </div>
</section>

<!-- ===== NEWSLETTER ===== -->
<section class="newsletter-section">
  <h2>Level Up Bareng Kami!</h2>
  <p>Claim kode diskon eksklusif, notifikasi flash sale, dan akses VIP ke promo terbaik.<br>Lebih cepat top up, lebih murah, lebih unggul.</p>
  <form class="newsletter-form" id="newsletterForm">
    <input type="email" placeholder="Masukan Email Kamu" id="newsletterEmail" required>
    <button type="submit" class="btn btn-solid">Subscribe Now</button>
  </form>
  <p class="newsletter-feedback" id="newsletterFeedback"></p>
</section>
@endsection
