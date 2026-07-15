// ============ DATA ============

const TESTIMONIALS = [
  { name: 'User Free Fire', game: 'Top Up - Free Fire', avatar: '🙂', quote: 'Top up Diamond Free Fire di sini cepat banget. Setelah pembayaran berhasil, diamond langsung masuk ke akun tanpa perlu menunggu lama.' },
  { name: 'User Mobile Legends', game: 'Top Up - Mobile Legends', avatar: '😄', quote: 'Top up Diamond MLBB cuma beberapa menit langsung masuk. Harganya juga lebih murah dibanding tempat lain. Sudah langganan dari lama dan selalu aman.' },
  { name: 'User PUBG Mobile', game: 'Top Up - PUBG Mobile', avatar: '🎮', quote: 'Top up UC PUBG Mobile menit langsung masuk ke akun. Harganya bersaing, prosesnya cepat, dan sejauh ini tanpa kendala. Sudah beberapa kali top up di sini dan hasilnya selalu memuaskan.' },
  { name: 'User Valorant', game: 'Top Up - Valorant', avatar: '🎯', quote: 'Poin Valorant masuk instan setelah bayar QRIS. Prosesnya jelas dan ada notifikasi tiap tahap. Recommended buat yang males ribet.' },
  { name: 'User Genshin Impact', game: 'Top Up - Genshin Impact', avatar: '💎', quote: 'Genesis Crystal masuk kurang dari 5 menit. CS-nya responsif kalau ada kendala. Harga juga bersahabat buat dompet pelajar seperti saya.' },
];

const LEADERBOARD = [
  { name: 'Rizky_Ace', amount: 'Rp 4.850.000' },
  { name: 'Dinda.ML', amount: 'Rp 3.920.000' },
  { name: 'ProGamerID', amount: 'Rp 3.410.000' },
  { name: 'FaizFF07', amount: 'Rp 2.980.000' },
  { name: 'ValoQueen', amount: 'Rp 2.650.000' },
];

const GRADIENTS = [
  'linear-gradient(160deg,#1e3a5f,#0f1c2e)',
  'linear-gradient(160deg,#3b2465,#1a1030)',
  'linear-gradient(160deg,#5c1f2e,#240d13)',
  'linear-gradient(160deg,#1f4d2e,#0d1f13)',
  'linear-gradient(160deg,#4a1f5c,#1a0d24)',
];

let selectedBrand = null;
let selectedNominal = null;
let selectedPay = null;
let paymentMethods = [];

// ============ HEADER SCROLL ============
const header = document.getElementById('siteHeader');
if (header) {
  window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 12);
  });
}

// ============ MOBILE MENU ============
const hamburgerBtn = document.getElementById('hamburgerBtn');
const mobileMenu = document.getElementById('mobileMenu');
if (hamburgerBtn && mobileMenu) {
  hamburgerBtn.addEventListener('click', () => {
    hamburgerBtn.classList.toggle('open');
    mobileMenu.classList.toggle('open');
  });
  mobileMenu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
    hamburgerBtn.classList.remove('open');
    mobileMenu.classList.remove('open');
  }));
}

// ============ AUTH DROPDOWN ============
document.querySelectorAll('.auth-dropdown-toggle').forEach(btn => {
  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    const menu = btn.nextElementSibling;
    if (menu) menu.classList.toggle('show');
  });
});
document.addEventListener('click', () => {
  document.querySelectorAll('.auth-dropdown-menu').forEach(m => m.classList.remove('show'));
});

// ============ HERO CAROUSEL ============
const heroSlides = document.querySelectorAll('.hero-slide');
const heroDots = document.getElementById('heroDots');
let heroIndex = 0;
if (heroSlides.length && heroDots) {
  heroSlides.forEach((_, i) => {
    const dot = document.createElement('span');
    dot.className = 'dot' + (i === 0 ? ' active' : '');
    dot.addEventListener('click', () => setHeroSlide(i));
    heroDots.appendChild(dot);
  });
}
function setHeroSlide(i) {
  if (!heroSlides[heroIndex] || !heroDots) return;
  heroSlides[heroIndex].classList.remove('active');
  heroDots.children[heroIndex].classList.remove('active');
  heroIndex = i;
  heroSlides[heroIndex].classList.add('active');
  heroDots.children[heroIndex].classList.add('active');
}
let heroTimer;
if (heroSlides.length) {
  heroTimer = setInterval(() => setHeroSlide((heroIndex + 1) % heroSlides.length), 5500);
  const carousel = document.querySelector('.hero-carousel');
  if (carousel) {
    carousel.addEventListener('mouseenter', () => clearInterval(heroTimer));
    carousel.addEventListener('mouseleave', () => {
      heroTimer = setInterval(() => setHeroSlide((heroIndex + 1) % heroSlides.length), 5500);
    });
  }
}

const pesanJokiBtn = document.getElementById('pesanJokiBtn');
if (pesanJokiBtn) {
  pesanJokiBtn.addEventListener('click', () => window.location.href = '/register');
}

// ============ FEATURED GAMES CAROUSEL ============
const featTrack = document.getElementById('featuredTrack');
const featWraps = featTrack ? featTrack.querySelectorAll('.featured-card-wrap') : [];
const featDots = document.getElementById('featuredDots');
const featBg = document.getElementById('featuredBg');
const featTitle = document.getElementById('featTitle');
const featDesc = document.getElementById('featDesc');
const featCta = document.getElementById('featCta');
let featIndex = 0;
let featTimer = null;

function getSlideWidth() {
  const carousel = document.getElementById('featuredCarousel');
  if (!carousel || !featWraps[0]) return 0;
  const wrapStyle = getComputedStyle(featWraps[0]);
  const pct = parseFloat(wrapStyle.flexBasis) || 60;
  const trackStyle = featTrack ? getComputedStyle(featTrack) : null;
  const gap = trackStyle ? parseFloat(trackStyle.columnGap || trackStyle.gap) || 0 : 0;
  return carousel.offsetWidth * (pct / 100) + gap;
}

function updateFeatLeft(i) {
  const wrap = featWraps[i];
  if (!wrap) return;
  if (featTitle) featTitle.textContent = wrap.dataset.brand || '';
  if (featDesc) featDesc.textContent = wrap.dataset.desc || '';
  if (featCta) {
    featCta.dataset.brand = wrap.dataset.brand || '';
    featCta.dataset.href = '/games/' + encodeURIComponent(wrap.dataset.brand || '');
  }
  const bg = wrap.dataset.bg || wrap.dataset.thumb;
  if (featBg && bg) {
    featBg.style.backgroundImage = `url('${bg}')`;
  }
}

function setFeatSlide(i) {
  if (!featWraps.length) return;
  const total = featWraps.length;
  featIndex = (i + total) % total;

  const offset = featIndex * getSlideWidth();
  if (featTrack) featTrack.style.transform = `translateX(-${offset}px)`;

  featWraps.forEach((w, idx) => w.classList.toggle('active', idx === featIndex));

  if (featDots) {
    Array.from(featDots.children).forEach((d, idx) => d.classList.toggle('active', idx === featIndex));
  }

  updateFeatLeft(featIndex);
}

function startFeatTimer() {
  stopFeatTimer();
  if (featWraps.length > 1) {
    featTimer = setInterval(() => setFeatSlide(featIndex + 1), 5000);
  }
}
function stopFeatTimer() {
  if (featTimer) { clearInterval(featTimer); featTimer = null; }
}

if (featWraps.length) {
  setFeatSlide(0);

  document.querySelector('.featured-prev')?.addEventListener('click', () => { stopFeatTimer(); setFeatSlide(featIndex - 1); startFeatTimer(); });
  document.querySelector('.featured-next')?.addEventListener('click', () => { stopFeatTimer(); setFeatSlide(featIndex + 1); startFeatTimer(); });

  if (featDots) {
    Array.from(featDots.children).forEach((dot, i) => {
      dot.addEventListener('click', () => { stopFeatTimer(); setFeatSlide(i); startFeatTimer(); });
    });
  }

  featWraps.forEach((wrap, i) => {
    wrap.addEventListener('click', () => {
      if (i === featIndex) {
        const brand = wrap.dataset.brand;
        if (brand) window.location.href = '/games/' + encodeURIComponent(brand);
        return;
      }
      stopFeatTimer();
      setFeatSlide(i);
      startFeatTimer();
    });
  });

  const carouselEl = document.getElementById('featuredCarousel');
  if (carouselEl) {
    carouselEl.addEventListener('mouseenter', stopFeatTimer);
    carouselEl.addEventListener('mouseleave', startFeatTimer);
  }
  startFeatTimer();

  if (featCta) {
    featCta.addEventListener('click', () => {
      const brand = featCta.dataset.brand;
      if (brand) window.location.href = '/games/' + encodeURIComponent(brand);
    });
  }

  let startX = 0, endX = 0;
  carouselEl?.addEventListener('touchstart', (e) => { startX = e.changedTouches[0].screenX; }, { passive: true });
  carouselEl?.addEventListener('touchend', (e) => {
    endX = e.changedTouches[0].screenX;
    const diff = startX - endX;
    if (Math.abs(diff) > 50) {
      stopFeatTimer();
      if (diff > 0) setFeatSlide(featIndex + 1);
      else setFeatSlide(featIndex - 1);
      startFeatTimer();
    }
  }, { passive: true });

  window.addEventListener('resize', () => setFeatSlide(featIndex));
}

// ============ TABS ============
document.querySelectorAll('.tab-pill').forEach(tab => {
  tab.addEventListener('click', () => {
    if (tab.id === 'cekTransaksiTab' || tab.dataset.filter === 'check') { openModal('checkModal'); return; }
    document.querySelectorAll('.tab-pill').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    const filter = tab.dataset.filter;
    filterGames(filter);
  });
});
document.getElementById('cekTransaksiLink')?.addEventListener('click', (e) => { e.preventDefault(); openModal('checkModal'); });
document.getElementById('leaderboardLink')?.addEventListener('click', (e) => { e.preventDefault(); openModal('leaderboardModal'); });

// ============ FILTER GAMES ============
function filterGames(filter) {
  const cards = document.querySelectorAll('.game-card');
  cards.forEach(card => {
    if (filter === 'all') {
      card.style.display = '';
    } else {
      const cat = card.dataset.category;
      card.style.display = cat === filter ? '' : 'none';
    }
  });
}

// ============ FETCH PRODUCTS ============
async function fetchProducts(brand) {
  try {
    const res = await fetch(`/api/products?brand=${encodeURIComponent(brand)}`);
    return await res.json();
  } catch (e) {
    return [];
  }
}

async function fetchPaymentMethods() {
  try {
    const res = await fetch('/api/payment-methods');
    return await res.json();
  } catch (e) {
    return [];
  }
}

// ============ SEARCH SUGGESTIONS ============
const searchInput = document.getElementById('searchInput');
const searchSuggest = document.getElementById('searchSuggest');

function bindSearch(input, suggestBox) {
  if (!input) return;
  let debounceTimer;
  input.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(async () => {
      const q = input.value.trim().toLowerCase();
      if (!q) { if (suggestBox) suggestBox.classList.remove('show'); return; }
      try {
        const res = await fetch(`/api/brands/search?q=${encodeURIComponent(q)}`);
        const matches = await res.json();
        if (suggestBox) {
          if (matches.length) {
            suggestBox.innerHTML = matches.map(m =>
              `<div class="search-suggest-item" data-brand="${m.brand}"><span>${getBrandIcon(m.brand)}</span> ${m.brand}</div>`
            ).join('');
          } else {
            suggestBox.innerHTML = `<div class="search-suggest-empty">Tidak ada game ditemukan untuk "${q}"</div>`;
          }
          suggestBox.classList.add('show');
          suggestBox.querySelectorAll('.search-suggest-item').forEach(item => {
            item.addEventListener('click', () => {
              const brand = item.dataset.brand;
              suggestBox.classList.remove('show');
              input.value = '';
              window.location.href = '/games/' + encodeURIComponent(brand);
            });
          });
        }
      } catch (e) {
        if (suggestBox) suggestBox.classList.remove('show');
      }
    }, 200);
  });
}
bindSearch(searchInput, searchSuggest);
bindSearch(document.getElementById('mobileSearchInput'), null);
document.addEventListener('click', (e) => {
  if (searchSuggest && !e.target.closest('.search-wrap') && !e.target.closest('.mobile-search-wrap')) {
    searchSuggest.classList.remove('show');
  }
});

function getBrandIcon(brand) {
  const card = document.querySelector(`.game-card[data-brand="${brand}"]`);
  return card ? (card.dataset.icon || '🎮') : '🎮';
}
function getBrandCategory(brand) {
  const card = document.querySelector(`.game-card[data-brand="${brand}"]`);
  return card ? (card.dataset.category || 'other') : 'other';
}

// ============ MODAL SYSTEM ============
function openModal(id) {
  document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open'));
  const el = document.getElementById(id);
  if (el) {
    el.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
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
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeAllModals(); });

// ============ TOPUP MODAL ============
const nominalGrid = document.getElementById('nominalGrid');
const paySelectGrid = document.getElementById('paySelectGrid');
const topupTotal = document.getElementById('topupTotal');

async function openTopupModal(brand) {
  selectedBrand = brand;
  selectedNominal = null;
  selectedPay = null;
  if (topupTotal) topupTotal.textContent = 'Rp 0';

  const nameEl = document.getElementById('topupGameName');
  const iconEl = document.getElementById('topupIcon');
  if (nameEl) nameEl.textContent = brand;
  if (iconEl) iconEl.textContent = getBrandIcon(brand);

  const cat = getBrandCategory(brand);
  const label = cat === 'moba' ? 'Diamond' : cat === 'fps' ? 'Poin' : cat === 'br' ? 'UC' : 'Item';

  if (nominalGrid) {
    nominalGrid.innerHTML = '<div style="grid-column:1/-1;text-align:center;color:var(--text-mute);padding:1rem;font-size:.85rem;">Memuat...</div>';
  }
  if (paySelectGrid) {
    paySelectGrid.innerHTML = '<div style="grid-column:1/-1;text-align:center;color:var(--text-mute);padding:1rem;font-size:.85rem;">Memuat...</div>';
  }

  const [products, payments] = await Promise.all([
    fetchProducts(brand),
    fetchPaymentMethods()
  ]);

  paymentMethods = payments;

  if (nominalGrid) {
    if (products.length) {
      nominalGrid.innerHTML = products.map((p, i) => `
        <div class="nominal-opt" data-idx="${i}" data-product='${JSON.stringify(p).replace(/'/g, "&#39;")}'>
          ${p.product_name}
          <span class="nominal-price">Rp ${Number(p.selling_price).toLocaleString('id-ID')}</span>
        </div>`).join('');
      nominalGrid.querySelectorAll('.nominal-opt').forEach(opt => {
        opt.addEventListener('click', () => {
          nominalGrid.querySelectorAll('.nominal-opt').forEach(o => o.classList.remove('selected'));
          opt.classList.add('selected');
          const raw = opt.dataset.product;
          try {
            selectedNominal = JSON.parse(raw.replace(/&#39;/g, "'"));
          } catch (e) {
            selectedNominal = { selling_price: 0 };
          }
          updateTotal();
        });
      });
    } else {
      nominalGrid.innerHTML = '<div style="grid-column:1/-1;text-align:center;color:var(--text-mute);padding:1rem;font-size:.85rem;">Tidak ada produk tersedia</div>';
    }
  }

  if (paySelectGrid) {
    if (payments.length) {
      paySelectGrid.innerHTML = payments.map(p => {
        const photo = p.photo_url || '';
        const icon = p.icon || '';
        const content = photo ? `<img src="${photo}" alt="${p.name}" class="pay-opt-img">` : (icon ? `<span class="pay-opt-icon">${icon}</span><span class="pay-opt-name">${p.name}</span>` : p.name);
        return `<div class="pay-opt" data-pay="${p.name}">${content}</div>`;
      }).join('');
      paySelectGrid.querySelectorAll('.pay-opt').forEach(opt => {
        opt.addEventListener('click', () => {
          paySelectGrid.querySelectorAll('.pay-opt').forEach(o => o.classList.remove('selected'));
          opt.classList.add('selected');
          selectedPay = opt.dataset.pay;
        });
      });
    } else {
      paySelectGrid.innerHTML = '<div style="grid-column:1/-1;text-align:center;color:var(--text-mute);padding:1rem;font-size:.85rem;">Metode pembayaran tidak tersedia</div>';
    }
  }

  openModal('topupModal');
}
function updateTotal() {
  if (topupTotal) {
    topupTotal.textContent = selectedNominal ? 'Rp ' + Number(selectedNominal.selling_price).toLocaleString('id-ID') : 'Rp 0';
  }
}

document.getElementById('topupForm')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  if (!selectedNominal) { showToast('Pilih nominal top up terlebih dahulu', true); return; }
  if (!selectedPay) { showToast('Pilih metode pembayaran terlebih dahulu', true); return; }

  const form = e.target;
  const formData = new FormData(form);
  formData.append('product_id', selectedNominal.id || '');
  formData.append('payment_method', selectedPay);

  try {
    const res = await fetch('/orders', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'Accept': 'application/json' },
      body: formData
    });
    const data = await res.json();
    if (data.redirect) {
      window.location.href = data.redirect;
    } else if (data.success) {
      closeAllModals();
      showToast('Pesanan berhasil dibuat!');
    } else {
      showToast(data.message || 'Gagal membuat pesanan', true);
    }
  } catch (err) {
    showToast('Terjadi kesalahan. Silakan coba lagi.', true);
  }
});

// ============ TESTIMONIALS CAROUSEL ============
const testiTrack = document.getElementById('testiTrack');
const testiDots = document.getElementById('testiDots');
let testiIndex = 1;

if (testiTrack) {
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

  if (testiDots) {
    TESTIMONIALS.forEach((_, i) => {
      const dot = document.createElement('span');
      dot.className = 'dot' + (i === testiIndex ? ' active' : '');
      dot.addEventListener('click', () => setTesti(i));
      testiDots.appendChild(dot);
    });
  }

  function setTesti(i) {
    testiIndex = i;
    document.querySelectorAll('.testi-card').forEach((c, idx) => c.classList.toggle('center', idx === i));
    if (testiDots) {
      Array.from(testiDots.children).forEach((d, idx) => d.classList.toggle('active', idx === i));
    }
    const firstCard = testiTrack.children[0];
    if (firstCard) {
      const cardWidth = firstCard.getBoundingClientRect().width + 19.2;
      const offset = (i - 1) * cardWidth;
      testiTrack.style.transform = `translateX(${-offset}px)`;
    }
  }

  setTimeout(() => setTesti(1), 50);
  let testiTimer = setInterval(() => setTesti((testiIndex + 1) % TESTIMONIALS.length), 6000);
  const testiCarousel = document.querySelector('.testi-carousel');
  if (testiCarousel) {
    testiCarousel.addEventListener('mouseenter', () => clearInterval(testiTimer));
    testiCarousel.addEventListener('mouseleave', () => {
      testiTimer = setInterval(() => setTesti((testiIndex + 1) % TESTIMONIALS.length), 6000);
    });
  }
  window.addEventListener('resize', () => { if (testiTrack.children[0]) setTesti(testiIndex); });
}

// ============ PAYMENT MARQUEE ============
const paymentTrack = document.getElementById('paymentTrack');

(async function initPaymentMarquee() {
  if (!paymentTrack) return;
  let methods = await fetchPaymentMethods();
  if (!methods.length) {
    methods = ['QRIS','GoPay','OVO','DANA','BCA','BRI','Mandiri','Alfamart','Indomaret'];
  }
  const items = methods.map(m => typeof m === 'string' ? { name: m, photo_url: null, icon: null } : m);
  const doubled = [...items, ...items];
  paymentTrack.innerHTML = '';
  doubled.forEach(p => {
    const badge = document.createElement('div');
    badge.className = 'pay-badge';
    if (p.photo_url) {
      badge.innerHTML = `<img src="${p.photo_url}" alt="${p.name}" class="pay-badge-img">`;
    } else if (p.icon) {
      badge.innerHTML = `<span class="pay-badge-icon">${p.icon}</span><span class="pay-badge-name">${p.name}</span>`;
    } else {
      badge.textContent = p.name;
    }
    paymentTrack.appendChild(badge);
  });
})();

// ============ LEADERBOARD ============
const leaderboardList = document.getElementById('leaderboardList');
if (leaderboardList) {
  LEADERBOARD.forEach((u, i) => {
    const item = document.createElement('div');
    item.className = 'leaderboard-item';
    item.innerHTML = `<div class="leaderboard-rank">${i + 1}</div><div class="leaderboard-name">${u.name}</div><div class="leaderboard-amount">${u.amount}</div>`;
    leaderboardList.appendChild(item);
  });
}

// ============ CHECK TRANSACTION ============
document.getElementById('checkForm')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const val = e.target.querySelector('input')?.value?.trim();
  const result = document.getElementById('checkResult');
  if (!val || !result) return;
  result.innerHTML = '<div style="text-align:center;color:var(--text-mute);padding:1rem;">Memeriksa...</div>';
  try {
    const res = await fetch(`/api/orders/check?q=${encodeURIComponent(val)}`);
    if (!res.ok) {
      result.innerHTML = `<div class="status-box"><span class="status-pill failed">Tidak Ditemukan</span><p style="font-size:.85rem;color:var(--text-dim);margin-top:.5rem;">Transaksi <strong style="color:var(--text)">${val}</strong> tidak ditemukan. Periksa kembali ID transaksi atau email Anda.</p></div>`;
      return;
    }
    const data = await res.json();
    const statusClass = data.status === 'success' ? 'status-pill' : (data.status === 'pending' ? 'status-pill pending' : 'status-pill failed');
    const statusLabel = data.status === 'success' ? 'Berhasil' : (data.status === 'pending' ? 'Pending' : 'Gagal');
    result.innerHTML = `
      <div class="status-box">
        <span class="${statusClass}">${statusLabel}</span>
        <p style="font-size:.85rem;color:var(--text-dim);margin-top:.5rem;">
          Transaksi <strong style="color:var(--text)">${data.order_id || val}</strong><br>
          ${data.product_name ? 'Produk: ' + data.product_name : ''}
          ${data.customer_number ? '<br>ID: ' + data.customer_number : ''}
          ${data.price ? '<br>Total: Rp ' + Number(data.price).toLocaleString('id-ID') : ''}
          ${data.processed_at ? '<br>Diproses: ' + data.processed_at : ''}
        </p>
      </div>`;
  } catch (err) {
    result.innerHTML = '<div class="status-box"><span class="status-pill failed">Error</span><p style="font-size:.85rem;color:var(--text-dim);margin-top:.5rem;">Gagal memeriksa transaksi. Coba lagi nanti.</p></div>';
  }
});

// ============ NEWSLETTER ============
document.getElementById('newsletterForm')?.addEventListener('submit', (e) => {
  e.preventDefault();
  const email = document.getElementById('newsletterEmail')?.value;
  const feedback = document.getElementById('newsletterFeedback');
  if (feedback && email) {
    feedback.textContent = `Terima kasih! Kode diskon telah dikirim ke ${email}`;
    e.target.reset();
    setTimeout(() => { feedback.textContent = ''; }, 5000);
  }
});

// ============ TOAST ============
let toastTimer;
function showToast(msg, isError = false) {
  const toast = document.getElementById('toast');
  if (!toast) return;
  toast.textContent = msg;
  toast.className = 'toast' + (isError ? ' error' : ' success');
  toast.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => toast.classList.remove('show'), 3500);
}

// ============ FLASH MESSAGES ============
(function showFlashMessages() {
  const flash = document.getElementById('flash-data');
  if (flash) {
    try {
      const data = JSON.parse(flash.textContent);
      if (data.success) showToast(data.success);
      if (data.error) showToast(data.error, true);
    } catch (e) {}
    flash.remove();
  }
})();
