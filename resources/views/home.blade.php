@extends('layouts.topup')

@section('content')
<!-- ===== HERO ===== -->
<section class="hero-section" id="joki">
  <div class="hero-carousel">
    <div class="hero-slide active" data-slide="0">
      <div class="hero-tag">
        <img src="{{ asset('logo.png') }}" alt="Johen Gaming" class="hero-logo-img"> JOHEN GAMING
      </div>
      <h1 class="hero-title">
        JASA JOKI<br>
        <span class="accent">RANK PROFESIONAL</span>
      </h1>
      <p class="hero-sub">Dikerjakan oleh player berpengalaman.<br>Aman, cepat, dan progress terjamin.</p>
      <button class="btn btn-solid btn-lg" id="pesanJokiBtn">Pesan Joki Sekarang <span>›</span></button>
      <div class="hero-badges">
        <div class="hero-badge">
          <span class="badge-icon">🏆</span>
          <span>Joki<br>Berpengalaman</span>
        </div>
        <div class="hero-badge">
          <span class="badge-icon">🛡️</span>
          <span>Terpercaya<br>100%</span>
        </div>
        <div class="hero-badge">
          <span class="badge-icon">🚀</span>
          <span>Cepat &<br>Terjamin</span>
        </div>
      </div>
    </div>
    <div class="hero-slide" data-slide="1">
      <div class="hero-tag"><img src="{{ asset('logo.png') }}" alt="Johen Gaming" class="hero-logo-img"> JOHEN GAMING</div>
      <h1 class="hero-title">TOP UP DIAMOND<br><span class="accent">HARGA TERMURAH</span></h1>
      <p class="hero-sub">Instan masuk ke akun kamu.<br>Ratusan game tersedia 24 jam.</p>
      <button class="btn btn-solid btn-lg" onclick="document.getElementById('games').scrollIntoView({behavior:'smooth'})">Top Up Sekarang <span>›</span></button>
      <div class="hero-badges">
        <div class="hero-badge"><span class="badge-icon">⚡</span><span>Proses<br>Instan</span></div>
        <div class="hero-badge"><span class="badge-icon">💳</span><span>Metode<br>Lengkap</span></div>
        <div class="hero-badge"><span class="badge-icon">⭐</span><span>Rating<br>4.9/5</span></div>
      </div>
    </div>
    <div class="hero-slide" data-slide="2">
      <div class="hero-tag"><img src="{{ asset('logo.png') }}" alt="Johen Gaming" class="hero-logo-img"> JOHEN GAMING</div>
      <h1 class="hero-title">EVENT SPESIAL<br><span class="accent">DISKON 20%</span></h1>
      <p class="hero-sub">Khusus member baru minggu ini.<br>Jangan sampai kehabisan!</p>
      <button class="btn btn-solid btn-lg" data-open-modal="registerModal">Daftar Sekarang <span>›</span></button>
      <div class="hero-badges">
        <div class="hero-badge"><span class="badge-icon">🎁</span><span>Bonus<br>Member</span></div>
        <div class="hero-badge"><span class="badge-icon">🔥</span><span>Promo<br>Terbatas</span></div>
        <div class="hero-badge"><span class="badge-icon">🕒</span><span>Buka<br>24 Jam</span></div>
      </div>
    </div>

    <div class="hero-art" aria-hidden="true">
      <div class="hero-art-glow"></div>
      <div class="hero-rank-badge">
        <div class="rank-shield">
          <span class="rank-diamond">◆</span>
        </div>
        <div class="rank-stars">★ ★ ★</div>
      </div>
      <div class="hero-warrior">🛡️</div>
      <div class="hero-help-card">
        <p class="help-title">Susah Naik Rank?</p>
        <p class="help-strong">SERAHKAN PADA KAMI!</p>
        <p class="help-sub">Tinggal duduk, rank naik.</p>
      </div>
    </div>
  </div>
  <div class="hero-dots" id="heroDots"></div>
</section>

<!-- ===== CATEGORY TABS ===== -->
<section class="tabs-section">
  <div class="tabs-wrap" id="tabsWrap">
    <button class="tab-pill active" data-filter="all">Top Up Games</button>
    <button class="tab-pill" data-filter="moba" id="jokiTab">Joki Mobile Legends</button>
    <button class="tab-pill" data-filter="check" id="cekTransaksiTab">Cek Transaksi</button>
  </div>
</section>

<!-- ===== SECTION HEADER ===== -->
<section class="section-heading">
  <h2>PRODUK UNGGULAN</h2>
  <p>Pilihan terbaik dengan transaksi terbanyak dan rating tertinggi dari pengguna.</p>
</section>

<!-- ===== FEATURED GAMES CAROUSEL ===== -->
@php
  $featGames = $popularBrands;
  $first = $featGames->first();
  $firstBg = $first ? ($first->carousel_bg_url ?? $first->thumbnail_url ?? '') : '';
@endphp
<section class="featured-section" id="topup">
  <div class="featured-bg" id="featuredBg" style="background-image:{{ $firstBg ? "url('{$firstBg}')" : 'none' }};">
    <div class="featured-bg-overlay"></div>
  </div>
  <div class="featured-inner">
    @if($featGames->count())
    <div class="featured-left" id="featLeft">
      <span class="featured-eyebrow">GAME POPULER</span>
      <h2 class="featured-title" id="featTitle">{{ $first->name }}</h2>
      <p class="featured-desc" id="featDesc">{{ $first->description ?? '' }}</p>
      <button class="featured-cta" id="featCta">Top Up Sekarang <span>→</span></button>
    </div>
    <div class="featured-right">
      <div class="featured-carousel" id="featuredCarousel">
        <div class="featured-track" id="featuredTrack">
          @foreach($featGames as $i => $b)
            <div class="featured-card-wrap{{ $i === 0 ? ' active' : '' }}"
                 data-index="{{ $i }}"
                 data-brand="{{ $b->name }}"
             data-category="{{ $b->category ?? 'other' }}"
             data-service-type="{{ $b->service_type ?? 'topup' }}"
             data-thumb="{{ $b->thumbnail_url ?? '' }}"
                 data-bg="{{ $b->carousel_bg_url ?? $b->thumbnail_url ?? '' }}"
                 data-desc="{{ $b->description ?? '' }}">
              <div class="featured-card-glow"></div>
              <div class="featured-card">
                @if($b->thumbnail_url)
                  <img src="{{ $b->thumbnail_url }}" alt="{{ $b->name }}" class="featured-card-img" loading="lazy">
                @endif
                <div class="featured-card-badge">{{ $b->name }}</div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
      <div class="featured-controls">
        <button class="featured-nav featured-prev" aria-label="Previous">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <div class="featured-dots" id="featuredDots">
          @foreach($featGames as $i => $b)
            <span class="dot{{ $i === 0 ? ' active' : '' }}" data-index="{{ $i }}"></span>
          @endforeach
        </div>
        <button class="featured-nav featured-next" aria-label="Next">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
      </div>
    </div>
    @else
    <div class="featured-left">
      <span class="featured-eyebrow">GAME POPULER</span>
      <h2 class="featured-title">Belum Ada Game Populer</h2>
      <p class="featured-desc">Admin belum menandai game populer. Atur di panel admin &rarr; Daftar Game &rarr; centang "Populer".</p>
    </div>
    <div class="featured-right">
      <div class="featured-card" style="background:var(--surface-2);border-radius:20px;aspect-ratio:4/5;display:flex;align-items:center;justify-content:center;color:var(--text-mute);font-size:1rem;">—</div>
    </div>
    @endif
  </div>
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
      @endphp
      <a href="{{ route('games.show', $brand->name) }}" class="game-card"
         data-brand="{{ $brand->name }}"
         data-category="{{ $brand->category ?? 'other' }}"
         data-service-type="{{ $brand->service_type ?? 'topup' }}"
         data-icon="{{ $icon }}"
         data-thumbnail="{{ $brand->thumbnail_url ?? '' }}"
         style="background:{{ $bg }};animation:cardIn .5s ease forwards;animation-delay:{{ $i * 0.04 }}s;opacity:0;transform:translateY(16px);">
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
</section>

<style>
@keyframes cardIn{to{opacity:1;transform:translateY(0);}}
</style>

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
