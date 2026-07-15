<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Johen Gaming — Top Up & Joki Termurah')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="{{ asset('css/topup.css') }}">
@stack('styles')
</head>
<body>

<!-- ===== HEADER ===== -->
<header class="site-header" id="siteHeader">
  <div class="header-inner">
    <a href="{{ route('home') }}" class="logo">
      <img src="{{ asset('logo.png') }}" alt="Johen Gaming" class="logo-img">
      <span class="logo-text">JOHEN<span>GAMING</span></span>
    </a>

    <div class="search-wrap">
      <svg class="search-icon" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M20 20L16.5 16.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      <input type="text" id="searchInput" placeholder="Cari Game atau Voucher" autocomplete="off">
      <div class="search-suggest" id="searchSuggest"></div>
    </div>

    <nav class="main-nav" id="mainNav">
      <a href="{{ route('home') }}#topup">Top Up</a>
      <a href="{{ route('home') }}#joki">Joki</a>
      <a href="#" data-open-modal="checkModal">Cek Transaksi</a>
      <a href="#" data-open-modal="leaderboardModal">Leaderboard</a>
    </nav>

    @auth
      <div class="auth-user">
        <div class="auth-dropdown">
          <button class="auth-dropdown-toggle" style="display:flex;align-items:center;gap:.6rem;background:none;border:none;cursor:pointer;color:inherit;padding:.3rem;border-radius:8px;">
            <div class="auth-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <span class="auth-name">{{ Auth::user()->name }}</span>
          </button>
          <div class="auth-dropdown-menu">
            <a href="{{ route('dashboard') }}" class="auth-dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
              Dashboard
            </a>
            <a href="{{ route('orders.my') }}" class="auth-dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
              Pesanan Saya
            </a>
            @if(Auth::user()->isAdmin())
              <a href="{{ route('admin.dashboard') }}" class="auth-dropdown-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                Admin Panel
              </a>
            @endif
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
              @csrf
              <button type="submit" class="auth-dropdown-item logout" style="width:100%;text-align:left;background:none;border:none;cursor:pointer;font-family:inherit;font-size:.82rem;padding:.55rem .7rem;border-radius:8px;display:flex;align-items:center;gap:.5rem;color:#f87171;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Logout
              </button>
            </form>
          </div>
        </div>
      </div>
    @else
      <div class="auth-buttons">
        <a href="{{ route('login') }}" class="btn btn-outline">Masuk</a>
        <a href="{{ route('register') }}" class="btn btn-solid">Daftar</a>
      </div>
    @endauth

    <button class="hamburger" id="hamburgerBtn" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>

  <div class="mobile-menu" id="mobileMenu">
    <div class="mobile-search-wrap">
      <svg class="search-icon" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M20 20L16.5 16.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      <input type="text" placeholder="Cari Game atau Voucher" id="mobileSearchInput">
    </div>
    <a href="{{ route('home') }}#topup">Top Up</a>
    <a href="{{ route('home') }}#joki">Joki</a>
    <a href="#" data-open-modal="checkModal">Cek Transaksi</a>
    <a href="#" data-open-modal="leaderboardModal">Leaderboard</a>
    @auth
      <a href="{{ route('dashboard') }}">Dashboard</a>
      <a href="{{ route('orders.my') }}">Pesanan Saya</a>
      @if(Auth::user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}">Admin Panel</a>
      @endif
      <div class="mobile-auth">
        <form method="POST" action="{{ route('logout') }}" style="width:100%;">
          @csrf
          <button type="submit" class="btn btn-outline" style="width:100%;justify-content:center;">Logout</button>
        </form>
      </div>
    @else
      <div class="mobile-auth">
        <a href="{{ route('login') }}" class="btn btn-outline" style="flex:1;justify-content:center;">Masuk</a>
        <a href="{{ route('register') }}" class="btn btn-solid" style="flex:1;justify-content:center;">Daftar</a>
      </div>
    @endauth
  </div>
</header>

<main>
  @if(session('success') || session('error'))
    <div id="flash-data" style="display:none;">{{ json_encode(['success' => session('success'), 'error' => session('error')]) }}</div>
  @endif
  @yield('content')
</main>

<!-- ===== FOOTER ===== -->
<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <div class="logo">
        <img src="{{ asset('logo.png') }}" alt="Johen Gaming" class="logo-img">
        <span class="logo-text">JOHEN<span>GAMING</span></span>
      </div>
      <p>Top up game & voucher terlaris, murah, aman legal 100% buka 24 jam dengan payment terlengkap Indonesia.</p>
    </div>
    <div class="footer-col">
      <h4>Peta Situs</h4>
      <a href="{{ route('home') }}">Beranda</a>
      <a href="#" data-open-modal="checkModal">Cek Transaksi</a>
      <a href="#">Hubungi Kami</a>
      <a href="#">Ulasan</a>
    </div>
    <div class="footer-col">
      <h4>Dukungan</h4>
      <a href="#">Contact Us</a>
      <a href="#">Affiliate Program</a>
      <a href="#">FAQ</a>
    </div>
    <div class="footer-col">
      <h4>Legalitas</h4>
      <a href="#">Kebijakan Privasi</a>
      <a href="#">Syarat & Ketentuan</a>
    </div>
  </div>
  <div class="footer-bottom">© {{ date('Y') }} Johen Gaming. All Rights Reserved.</div>
</footer>

<!-- ===== MODALS ===== -->
<!-- Topup Modal -->
<div class="modal-overlay" id="topupModal">
  <div class="modal-box modal-box-wide">
    <button class="modal-close" data-close-modal>&times;</button>
    <div class="topup-header">
      <div class="topup-icon" id="topupIcon">🎮</div>
      <div>
        <h3 id="topupGameName">Nama Game</h3>
        <p class="modal-sub">Isi data akun dan pilih nominal top up.</p>
      </div>
    </div>
    <form class="modal-form" id="topupForm">
      @csrf
      <label>User ID
        <input type="text" name="customer_number" required placeholder="Masukkan User ID">
      </label>
      <label>Zone ID / Server (opsional)
        <input type="text" name="customer_name" placeholder="Contoh: 2001">
      </label>
      <p class="field-label">Pilih Nominal</p>
      <div class="nominal-grid" id="nominalGrid"></div>
      <p class="field-label">Metode Pembayaran</p>
      <div class="pay-select-grid" id="paySelectGrid"></div>
      <div class="topup-total">
        <span>Total Pembayaran</span>
        <strong id="topupTotal">Rp 0</strong>
      </div>
      <button type="submit" class="btn btn-solid btn-full">Beli Sekarang</button>
    </form>
  </div>
</div>

<div class="modal-overlay" id="checkModal">
  <div class="modal-box">
    <button class="modal-close" data-close-modal>&times;</button>
    <h3>Cek Transaksi</h3>
    <p class="modal-sub">Masukkan ID Transaksi atau email kamu untuk melihat status pesanan.</p>
    <form class="modal-form" id="checkForm">
      <label>ID Transaksi / Email
        <input type="text" required placeholder="TRX-XXXXXX atau email">
      </label>
      <button type="submit" class="btn btn-solid btn-full">Cek Status</button>
    </form>
    <div class="check-result" id="checkResult"></div>
  </div>
</div>

<div class="modal-overlay" id="leaderboardModal">
  <div class="modal-box">
    <button class="modal-close" data-close-modal>&times;</button>
    <h3>Leaderboard Top Up</h3>
    <p class="modal-sub">Pengguna dengan transaksi terbanyak bulan ini.</p>
    <div class="leaderboard-list" id="leaderboardList"></div>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<script src="{{ asset('js/topup.js') }}"></script>
@stack('scripts')
</body>
</html>
