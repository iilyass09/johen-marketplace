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
<link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
<link rel="shortcut icon" href="{{ asset('logo.png') }}">
<link rel="stylesheet" href="{{ asset('css/topup.css') }}?v=2">
@stack('styles')
</head>
<body>

<!-- ===== HEADER ===== -->
<header class="site-header" id="siteHeader">
  <div class="header-inner">
    <a href="{{ route('home') }}" class="logo">
      <img src="{{ asset('logo.png') }}" alt="Johen Gaming" class="logo-img">
    </a>

    <div class="search-wrap">
      <svg class="search-icon" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M20 20L16.5 16.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      <input type="text" id="searchInput" placeholder="Cari Game atau Voucher" autocomplete="off">
      <div class="search-suggest" id="searchSuggest"></div>
    </div>

    <nav class="main-nav" id="mainNav">
      <a href="{{ route('home') }}" class="{{ request()->routeIs('home') || request()->routeIs('games.show') ? 'active' : '' }}">Top Up</a>
      <a href="{{ route('jual-beli-akun') }}" class="{{ request()->routeIs('jual-beli-akun*') ? 'active' : '' }}">Jual Beli Akun</a>
      <a href="{{ route('check.transaction') }}" class="{{ request()->routeIs('check.transaction') ? 'active' : '' }}">Cek Transaksi</a>
      <a href="{{ route('leaderboard') }}" class="{{ request()->routeIs('leaderboard') ? 'active' : '' }}">Leaderboard</a>
    </nav>

    @auth
      <div class="auth-user">
        <div class="auth-dropdown">
          <button class="auth-dropdown-toggle">
            <div class="auth-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <span class="auth-name">{{ Auth::user()->name }}</span>
            <svg class="auth-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="auth-dropdown-menu">

            <a href="{{ route('orders.my') }}" class="auth-dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
              Pesanan Saya
            </a>
            <a href="{{ route('testimoni') }}" class="auth-dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/><line x1="9" y1="10" x2="15" y2="10"/><line x1="12" y1="7" x2="12" y2="13"/></svg>
              Ulasan
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

    <button class="nav-theme-btn" id="themeToggle" aria-label="Ganti tema">
      <svg class="icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
      <svg class="icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
    </button>

    <button class="hamburger" id="hamburgerBtn" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>

  <div class="mobile-menu" id="mobileMenu">
    <div class="mobile-search-wrap">
      <svg class="search-icon" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M20 20L16.5 16.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      <input type="text" placeholder="Cari Game atau Voucher" id="mobileSearchInput">
    </div>
    <button class="mobile-theme-btn" id="mobileThemeToggle" aria-label="Ganti tema">
      <span class="icon-wrap">
        <svg class="icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
        <svg class="icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
      </span>
      <span>Tema</span>
    </button>
    <a href="{{ route('home') }}#topup" class="{{ request()->routeIs('home') || request()->routeIs('games.show') ? 'active' : '' }}">Top Up</a>
    <a href="{{ route('jual-beli-akun') }}" class="{{ request()->routeIs('jual-beli-akun*') ? 'active' : '' }}">Jual Beli Akun</a>
    <a href="{{ route('check.transaction') }}" class="{{ request()->routeIs('check.transaction') ? 'active' : '' }}">Cek Transaksi</a>
    <a href="{{ route('leaderboard') }}" class="{{ request()->routeIs('leaderboard') ? 'active' : '' }}">Leaderboard</a>
    @auth
      <a href="{{ route('orders.my') }}">Pesanan Saya</a>
      <a href="{{ route('testimoni') }}">Ulasan</a>
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
      <a href="{{ route('check.transaction') }}">Cek Transaksi</a>
      <a href="{{ route('kontak') }}">Hubungi Kami</a>
      <a href="{{ route('testimoni') }}">Ulasan</a>
    </div>
    <div class="footer-col">
      <h4>Dukungan</h4>
      <a href="{{ route('kontak') }}">Contact Us</a>
      <a href="{{ route('faq') }}">FAQ</a>
    </div>
    <div class="footer-col">
      <h4>Legalitas</h4>
      <a href="{{ route('privacy') }}">Kebijakan Privasi</a>
      <a href="{{ route('terms') }}">Syarat & Ketentuan</a>
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

<!-- Toast -->
<div class="toast" id="toast"></div>

<a href="{{ route('kontak') }}" class="fab-cs" aria-label="Hubungi CS">
  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 14h3a2 2 0 012 2v3a2 2 0 01-2 2H5a2 2 0 01-2-2v-7a9 9 0 0118 0v7a2 2 0 01-2 2h-1a2 2 0 01-2-2v-3a2 2 0 012-2h3"/></svg>
</a>

<style>
[data-theme="light"] {
  --bg: #f4f1fa;
  --bg-soft: #e9e4f2;
  --surface: #ffffff;
  --surface-2: #f0ecf7;
  --surface-3: #e4def0;
  --border: rgba(0,0,0,.08);
  --border-strong: rgba(0,0,0,.14);
  --text: #1e1136;
  --text-dim: #5d4a82;
  --text-mute: #8e7ab3;
  --purple-glow: rgba(124,58,237,.25);
  --shadow-purple: 0 8px 30px -8px rgba(124,58,237,.35);
  --header-bg: rgba(244,241,250,.85);
  --header-bg-scrolled: rgba(244,241,250,.97);
  --nav-active-text:#4c1d95;
  --bg-card:#ffffff;
}
.nav-theme-btn {
  flex-shrink: 0;
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: var(--surface);
  color: var(--text-dim);
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid var(--border);
  cursor: pointer;
  transition: background .15s, color .15s, border-color .15s;
  position: relative;
}
.nav-theme-btn:hover {
  background: var(--surface-2);
  color: var(--text);
  border-color: var(--border-strong);
}
.nav-theme-btn .icon-sun,
.nav-theme-btn .icon-moon {
  position: absolute;
  transition: opacity .25s, transform .25s;
}
.mobile-theme-btn .icon-sun,
.mobile-theme-btn .icon-moon {
  transition: opacity .25s, transform .25s;
}
[data-theme="dark"] .nav-theme-btn .icon-sun,
html:not([data-theme="light"]) .nav-theme-btn .icon-sun,
[data-theme="dark"] .mobile-theme-btn .icon-sun,
html:not([data-theme="light"]) .mobile-theme-btn .icon-sun {
  opacity: 1;
  transform: rotate(0deg);
}
[data-theme="dark"] .nav-theme-btn .icon-moon,
html:not([data-theme="light"]) .nav-theme-btn .icon-moon,
[data-theme="dark"] .mobile-theme-btn .icon-moon,
html:not([data-theme="light"]) .mobile-theme-btn .icon-moon {
  opacity: 0;
  transform: rotate(90deg);
}
[data-theme="light"] .nav-theme-btn .icon-sun,
[data-theme="light"] .mobile-theme-btn .icon-sun {
  opacity: 0;
  transform: rotate(-90deg);
}
[data-theme="light"] .nav-theme-btn .icon-moon,
[data-theme="light"] .mobile-theme-btn .icon-moon {
  opacity: 1;
  transform: rotate(0deg);
}
.mobile-theme-btn {
  display: flex;
  align-items: center;
  gap: .7rem;
  padding: .7rem 0;
  font-size: .92rem;
  color: var(--text-dim);
  border: none;
  background: none;
  cursor: pointer;
  width: 100%;
  text-align: left;
  border-bottom: 1px solid var(--border);
  transition: color .2s;
  font-family: var(--font-body);
}
.mobile-theme-btn:hover {
  color: var(--text);
}
.mobile-theme-btn .icon-wrap {
  position: relative;
  width: 18px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.mobile-theme-btn .icon-wrap .icon-sun,
.mobile-theme-btn .icon-wrap .icon-moon {
  position: absolute;
}
.fab-cs {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 9999;
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background: var(--purple-light, #9d5cf5);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 20px rgba(157, 92, 245, .45);
  transition: transform .2s, box-shadow .2s;
  text-decoration: none;
}
.fab-cs:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 28px rgba(157, 92, 245, .55);
}
</style>

<script>
(function() {
  const theme = localStorage.getItem('theme') || 'dark';
  document.documentElement.setAttribute('data-theme', theme);
  function toggleTheme() {
    const html = document.documentElement;
    const current = html.getAttribute('data-theme');
    const next = current === 'light' ? 'dark' : 'light';
    html.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    document.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: next } }));
  }
  document.getElementById('themeToggle').addEventListener('click', toggleTheme);
  var mobileBtn = document.getElementById('mobileThemeToggle');
  if (mobileBtn) mobileBtn.addEventListener('click', toggleTheme);
})();
</script>

<script src="{{ asset('js/topup.js') }}?v=2"></script>
@stack('scripts')
</body>
</html>
