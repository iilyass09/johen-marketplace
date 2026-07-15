// ============ DATA ============
const GAMES = [
  { name: 'PUBG Mobile', icon: '🪖', cat: 'battle royale', filter: 'br', price: 'Mulai Rp 5.000' },
  { name: 'Mobile Legends', icon: '⚔️', cat: 'moba', filter: 'moba', price: 'Mulai Rp 3.000' },
  { name: 'Free Fire', icon: '🔥', cat: 'battle royale', filter: 'br', price: 'Mulai Rp 2.500' },
  { name: 'Roblox', icon: '🧱', cat: 'sandbox', filter: 'other', price: 'Mulai Rp 12.000' },
  { name: 'Valorant', icon: '🎯', cat: 'fps', filter: 'fps', price: 'Mulai Rp 15.000' },
  { name: 'Honor of Kings', icon: '👑', cat: 'moba', filter: 'moba', price: 'Mulai Rp 4.000' },
  { name: 'Arena of Valor', icon: '🏹', cat: 'moba', filter: 'moba', price: 'Mulai Rp 4.000' },
  { name: 'Point Blank', icon: '🔫', cat: 'fps', filter: 'fps', price: 'Mulai Rp 8.000' },
  { name: 'Call of Duty Mobile', icon: '💥', cat: 'fps', filter: 'fps', price: 'Mulai Rp 9.000' },
  { name: 'Arena Breakout', icon: '🎖️', cat: 'fps', filter: 'fps', price: 'Mulai Rp 10.000' },
  { name: 'Genshin Impact', icon: '💎', cat: 'rpg', filter: 'other', price: 'Mulai Rp 16.000' },
  { name: 'Clash of Clans', icon: '🏰', cat: 'strategi', filter: 'other', price: 'Mulai Rp 7.000' },
  { name: 'Clash Royale', icon: '🛡️', cat: 'strategi', filter: 'other', price: 'Mulai Rp 7.000' },
  { name: 'Asphalt 9', icon: '🏎️', cat: 'racing', filter: 'other', price: 'Mulai Rp 20.000' },
  { name: 'CrossFire', icon: '🎮', cat: 'fps', filter: 'fps', price: 'Mulai Rp 6.000' },
];

const TESTIMONIALS = [
  { name: 'User Free Fire', game: 'Top Up - Free Fire', avatar: '🙂', quote: 'Top up Diamond Free Fire di sini cepat banget. Setelah pembayaran berhasil, diamond langsung masuk ke akun tanpa perlu menunggu lama.' },
  { name: 'User Mobile Legends', game: 'Top Up - Mobile Legends', avatar: '😄', quote: 'Top up Diamond MLBB cuma beberapa menit langsung masuk. Harganya juga lebih murah dibanding tempat lain. Sudah langganan dari lama dan selalu aman.' },
  { name: 'User PUBG Mobile', game: 'Top Up - PUBG Mobile', avatar: '🎮', quote: 'Top up UC PUBG Mobile menit langsung masuk ke akun. Harganya bersaing, prosesnya cepat, dan sejauh ini tanpa kendala. Sudah beberapa kali top up di sini dan hasilnya selalu memuaskan.' },
  { name: 'User Valorant', game: 'Top Up - Valorant', avatar: '🎯', quote: 'Poin Valorant masuk instan setelah bayar QRIS. Prosesnya jelas dan ada notifikasi tiap tahap. Recommended buat yang males ribet.' },
  { name: 'User Genshin Impact', game: 'Top Up - Genshin Impact', avatar: '💎', quote: 'Genesis Crystal masuk kurang dari 5 menit. CS-nya responsif kalau ada kendala. Harga juga bersahabat buat dompet pelajar seperti saya.' },
];

const PAYMENTS = ['BRI','BNI','GoPay','OVO','DANA','ShopeePay','BCA','Mandiri','LinkAja','QRIS','Alfamart','Indomaret'];

const LEADERBOARD = [
  { name: 'Rizky_Ace', amount: 'Rp 4.850.000' },
  { name: 'Dinda.ML', amount: 'Rp 3.920.000' },
  { name: 'ProGamerID', amount: 'Rp 3.410.000' },
  { name: 'FaizFF07', amount: 'Rp 2.980.000' },
  { name: 'ValoQueen', amount: 'Rp 2.650.000' },
];

const NOMINALS = [
  { label: '50 Diamond', price: 15000 },
  { label: '100 Diamond', price: 28000 },
  { label: '250 Diamond', price: 65000 },
  { label: '500 Diamond', price: 125000 },
  { label: '1000 Diamond', price: 240000 },
  { label: '2000 Diamond', price: 470000 },
];

let selectedNominal = null;
let selectedPay = null;

// ============ HEADER SCROLL ============
const header = document.getElementById('siteHeader');
window.addEventListener('scroll', () => {
  header.classList.toggle('scrolled', window.scrollY > 12);
});

// ============ MOBILE MENU ============
const hamburgerBtn = document.getElementById('hamburgerBtn');
const mobileMenu = document.getElementById('mobileMenu');
hamburgerBtn.addEventListener('click', () => {
  hamburgerBtn.classList.toggle('open');
  mobileMenu.classList.toggle('open');
});
mobileMenu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
  hamburgerBtn.classList.remove('open');
  mobileMenu.classList.remove('open');
}));

// ============ HERO CAROUSEL ============
const heroSlides = document.querySelectorAll('.hero-slide');
const heroDots = document.getElementById('heroDots');
let heroIndex = 0;
heroSlides.forEach((_, i) => {
  const dot = document.createElement('span');
  dot.className = 'dot' + (i === 0 ? ' active' : '');
  dot.addEventListener('click', () => setHeroSlide(i));
  heroDots.appendChild(dot);
});
function setHeroSlide(i) {
  heroSlides[heroIndex].classList.remove('active');
  heroDots.children[heroIndex].classList.remove('active');
  heroIndex = i;
  heroSlides[heroIndex].classList.add('active');
  heroDots.children[heroIndex].classList.add('active');
}
let heroTimer = setInterval(() => setHeroSlide((heroIndex + 1) % heroSlides.length), 5500);
document.querySelector('.hero-carousel').addEventListener('mouseenter', () => clearInterval(heroTimer));
document.querySelector('.hero-carousel').addEventListener('mouseleave', () => {
  heroTimer = setInterval(() => setHeroSlide((heroIndex + 1) % heroSlides.length), 5500);
});

document.getElementById('pesanJokiBtn').addEventListener('click', () => openModal('registerModal'));

// ============ TABS ============
document.querySelectorAll('.tab-pill').forEach(tab => {
  tab.addEventListener('click', () => {
    if (tab.id === 'cekTransaksiTab') { openModal('checkModal'); return; }
    document.querySelectorAll('.tab-pill').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    const filter = tab.dataset.filter;
    renderGames(filter);
  });
});
document.getElementById('cekTransaksiLink').addEventListener('click', (e) => { e.preventDefault(); openModal('checkModal'); });
document.getElementById('leaderboardLink').addEventListener('click', (e) => { e.preventDefault(); openModal('leaderboardModal'); });

// ============ GAMES GRID ============
const gamesGrid = document.getElementById('gamesGrid');
const loadMoreBtn = document.getElementById('loadMoreBtn');
let showAll = false;

function renderGames(filter = 'all') {
  gamesGrid.innerHTML = '';
  const filtered = filter === 'all' ? GAMES : GAMES.filter(g => g.filter === filter || (filter === 'moba' && g.filter === 'moba'));
  filtered.forEach((g, i) => {
    const card = document.createElement('div');
    card.className = 'game-card';
    card.style.background = cardGradient(i);
    card.style.animationDelay = (i * 0.04) + 's';
    if (i >= 10 && !showAll) card.classList.add('hidden-extra');
    card.innerHTML = `
      <div class="game-card-icon">${g.icon}</div>
      <div class="game-card-overlay"></div>
      <div class="game-card-info">
        <div class="game-card-name">${g.name}</div>
        <div class="game-card-cat">${g.cat}</div>
      </div>`;
    card.addEventListener('click', () => openTopupModal(g.name, g.icon, g.filter));
    gamesGrid.appendChild(card);
  });
  loadMoreBtn.style.display = filtered.length > 10 ? 'block' : 'none';
  loadMoreBtn.textContent = showAll ? 'Tampilkan Lebih Sedikit' : 'Tampilkan Lainnya...';
}

const gradients = [
  'linear-gradient(160deg,#1e3a5f,#0f1c2e)',
  'linear-gradient(160deg,#3b2465,#1a1030)',
  'linear-gradient(160deg,#5c1f2e,#240d13)',
  'linear-gradient(160deg,#1f4d2e,#0d1f13)',
  'linear-gradient(160deg,#4a1f5c,#1a0d24)',
];
function cardGradient(i) { return gradients[i % gradients.length]; }

loadMoreBtn.addEventListener('click', () => {
  showAll = !showAll;
  const activeFilter = document.querySelector('.tab-pill.active')?.dataset.filter || 'all';
  renderGames(activeFilter);
  if (!showAll) document.getElementById('games').scrollIntoView({ behavior: 'smooth' });
});

renderGames();

// ============ TESTIMONIALS CAROUSEL ============
const testiTrack = document.getElementById('testiTrack');
const testiDots = document.getElementById('testiDots');
let testiIndex = 1;

TESTIMONIALS.forEach(t => {
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
  testiTrack.appendChild(card);
});
TESTIMONIALS.forEach((_, i) => {
  const dot = document.createElement('span');
  dot.className = 'dot' + (i === testiIndex ? ' active' : '');
  dot.addEventListener('click', () => setTesti(i));
  testiDots.appendChild(dot);
});

function setTesti(i) {
  testiIndex = i;
  document.querySelectorAll('.testi-card').forEach((c, idx) => c.classList.toggle('center', idx === i));
  Array.from(testiDots.children).forEach((d, idx) => d.classList.toggle('active', idx === i));
  const cardWidth = testiTrack.children[0].getBoundingClientRect().width + 19.2;
  const offset = (i - 1) * cardWidth;
  testiTrack.style.transform = `translateX(${-offset}px)`;
}
setTimeout(() => setTesti(1), 50);
let testiTimer = setInterval(() => setTesti((testiIndex + 1) % TESTIMONIALS.length), 6000);
document.querySelector('.testi-carousel').addEventListener('mouseenter', () => clearInterval(testiTimer));
document.querySelector('.testi-carousel').addEventListener('mouseleave', () => {
  testiTimer = setInterval(() => setTesti((testiIndex + 1) % TESTIMONIALS.length), 6000);
});
window.addEventListener('resize', () => setTesti(testiIndex));

// ============ PAYMENT MARQUEE ============
const paymentTrack = document.getElementById('paymentTrack');
const paymentIcons = { BRI:'🏦', BNI:'🏦', GoPay:'💚', OVO:'💜', DANA:'💙', ShopeePay:'🧡', BCA:'🏛️', Mandiri:'🏛️', LinkAja:'❤️', QRIS:'▦', Alfamart:'🔴', Indomaret:'🔵' };
[...PAYMENTS, ...PAYMENTS].forEach(p => {
  const badge = document.createElement('div');
  badge.className = 'pay-badge';
  badge.innerHTML = `<span>${paymentIcons[p] || '💳'}</span> ${p}`;
  paymentTrack.appendChild(badge);
});

// ============ LEADERBOARD ============
const leaderboardList = document.getElementById('leaderboardList');
LEADERBOARD.forEach((u, i) => {
  const item = document.createElement('div');
  item.className = 'leaderboard-item';
  item.innerHTML = `<div class="leaderboard-rank">${i + 1}</div><div class="leaderboard-name">${u.name}</div><div class="leaderboard-amount">${u.amount}</div>`;
  leaderboardList.appendChild(item);
});

// ============ SEARCH SUGGESTIONS ============
const searchInput = document.getElementById('searchInput');
const searchSuggest = document.getElementById('searchSuggest');

function bindSearch(input, suggestBox) {
  input.addEventListener('input', () => {
    const q = input.value.trim().toLowerCase();
    if (!q) { suggestBox && suggestBox.classList.remove('show'); return; }
    const matches = GAMES.filter(g => g.name.toLowerCase().includes(q));
    if (suggestBox) {
      suggestBox.innerHTML = matches.length
        ? matches.map(m => `<div class="search-suggest-item" data-name="${m.name}"><span>${m.icon}</span> ${m.name}</div>`).join('')
        : `<div class="search-suggest-empty">Tidak ada game ditemukan untuk "${q}"</div>`;
      suggestBox.classList.add('show');
      suggestBox.querySelectorAll('.search-suggest-item').forEach(item => {
        item.addEventListener('click', () => {
          const g = GAMES.find(x => x.name === item.dataset.name);
          suggestBox.classList.remove('show');
          input.value = '';
          document.getElementById('games').scrollIntoView({ behavior: 'smooth' });
          setTimeout(() => openTopupModal(g.name, g.icon, g.filter), 400);
        });
      });
    }
  });
}
bindSearch(searchInput, searchSuggest);
bindSearch(document.getElementById('mobileSearchInput'), null);
document.addEventListener('click', (e) => {
  if (!e.target.closest('.search-wrap')) searchSuggest.classList.remove('show');
});

// ============ MODAL SYSTEM ============
function openModal(id) {
  document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open'));
  document.getElementById(id).classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeAllModals() {
  document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('open'));
  document.body.style.overflow = '';
}
document.querySelectorAll('[data-open-modal]').forEach(btn => {
  btn.addEventListener('click', () => openModal(btn.dataset.openModal));
});
document.querySelectorAll('[data-close-modal]').forEach(btn => {
  btn.addEventListener('click', closeAllModals);
});
document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', (e) => { if (e.target === overlay) closeAllModals(); });
});
document.querySelectorAll('[data-switch-modal]').forEach(link => {
  link.addEventListener('click', (e) => { e.preventDefault(); openModal(link.dataset.switchModal); });
});
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeAllModals(); });

// ============ TOPUP MODAL ============
const nominalGrid = document.getElementById('nominalGrid');
const paySelectGrid = document.getElementById('paySelectGrid');
const topupTotal = document.getElementById('topupTotal');

function openTopupModal(name, icon, filter) {
  document.getElementById('topupGameName').textContent = name;
  document.getElementById('topupIcon').textContent = icon;
  selectedNominal = null;
  selectedPay = null;
  topupTotal.textContent = 'Rp 0';

  const label = filter === 'moba' ? 'Diamond' : filter === 'fps' ? 'Poin' : filter === 'br' ? 'UC' : 'Item';
  nominalGrid.innerHTML = NOMINALS.map((n, i) => `
    <div class="nominal-opt" data-idx="${i}">
      ${n.label.replace('Diamond', label)}
      <span class="nominal-price">Rp ${n.price.toLocaleString('id-ID')}</span>
    </div>`).join('');
  nominalGrid.querySelectorAll('.nominal-opt').forEach(opt => {
    opt.addEventListener('click', () => {
      nominalGrid.querySelectorAll('.nominal-opt').forEach(o => o.classList.remove('selected'));
      opt.classList.add('selected');
      selectedNominal = NOMINALS[opt.dataset.idx];
      updateTotal();
    });
  });

  paySelectGrid.innerHTML = ['QRIS', 'GoPay', 'OVO', 'DANA', 'BCA', 'BRI', 'Mandiri', 'Alfamart'].map(p => `<div class="pay-opt">${p}</div>`).join('');
  paySelectGrid.querySelectorAll('.pay-opt').forEach(opt => {
    opt.addEventListener('click', () => {
      paySelectGrid.querySelectorAll('.pay-opt').forEach(o => o.classList.remove('selected'));
      opt.classList.add('selected');
      selectedPay = opt.textContent;
    });
  });

  openModal('topupModal');
}
function updateTotal() {
  topupTotal.textContent = selectedNominal ? 'Rp ' + selectedNominal.price.toLocaleString('id-ID') : 'Rp 0';
}

document.getElementById('topupForm').addEventListener('submit', (e) => {
  e.preventDefault();
  if (!selectedNominal) { showToast('⚠️ Pilih nominal top up terlebih dahulu'); return; }
  if (!selectedPay) { showToast('⚠️ Pilih metode pembayaran terlebih dahulu'); return; }
  closeAllModals();
  showToast(`✅ Pesanan ${document.getElementById('topupGameName').textContent} berhasil dibuat!`);
  e.target.reset();
});

// ============ AUTH FORMS ============
document.getElementById('loginForm').addEventListener('submit', (e) => {
  e.preventDefault();
  closeAllModals();
  showToast('✅ Berhasil masuk. Selamat datang kembali!');
  e.target.reset();
});
document.getElementById('registerForm').addEventListener('submit', (e) => {
  e.preventDefault();
  closeAllModals();
  showToast('✅ Akun berhasil dibuat. Selamat bergabung!');
  e.target.reset();
});

// ============ CHECK TRANSACTION ============
document.getElementById('checkForm').addEventListener('submit', (e) => {
  e.preventDefault();
  const val = e.target.querySelector('input').value.trim();
  const result = document.getElementById('checkResult');
  result.innerHTML = `
    <div class="status-box">
      <span class="status-pill">Berhasil</span>
      <p style="font-size:.85rem;color:var(--text-dim);">Transaksi <strong style="color:var(--text)">${val}</strong> telah selesai diproses dan item sudah dikirim ke akun tujuan.</p>
    </div>`;
});

// ============ NEWSLETTER ============
document.getElementById('newsletterForm').addEventListener('submit', (e) => {
  e.preventDefault();
  const email = document.getElementById('newsletterEmail').value;
  const feedback = document.getElementById('newsletterFeedback');
  feedback.textContent = `🎉 Terima kasih! Kode diskon telah dikirim ke ${email}`;
  e.target.reset();
  setTimeout(() => { feedback.textContent = ''; }, 5000);
});

// ============ TOAST ============
let toastTimer;
function showToast(msg) {
  const toast = document.getElementById('toast');
  toast.textContent = msg;
  toast.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => toast.classList.remove('show'), 3500);
}
