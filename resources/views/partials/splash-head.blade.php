{{-- Splash: gate + critical CSS. Wajib inline (tidak lewat topup.css) supaya
     overlay tetap tampil walau stylesheet eksternal masih diunduh / cached. --}}
<script>
(function () {
    'use strict';

    var root = document.documentElement;

    // Tema ditulis lebih awal supaya splash tidak berkedip gelap lalu melompat
    // ke terang. Nilai defaultnya sama dengan script toggle di footer layout.
    try {
        var saved = localStorage.getItem('theme');
        root.setAttribute('data-theme', (saved === 'light' || saved === 'dark') ? saved : 'dark');
    } catch (e) {
        root.setAttribute('data-theme', 'dark');
    }

    var KEY = 'jhm_splash_seen';
    var param = new URLSearchParams(window.location.search).get('splash');
    var standalone = (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches)
        || window.navigator.standalone === true;

    var show;
    if (param === '0') {
        show = false;
    } else if (param === '1') {
        show = true;
    } else if (standalone) {
        show = false;
    } else {
        try { show = sessionStorage.getItem(KEY) !== '1'; } catch (e) { show = true; }
    }

    root.setAttribute('data-splash', show ? 'on' : 'off');
})();
</script>
<style>
#jhm-splash {
    position: fixed;
    inset: 0;
    z-index: 2147483000;
    display: grid;
    place-items: center;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity .45s ease, visibility .45s ease;
    font-family: 'Sora', 'Inter', system-ui, sans-serif;
    background:
        radial-gradient(120% 80% at 50% 0%, rgba(124, 58, 237, .30), transparent 62%),
        radial-gradient(90% 60% at 50% 100%, rgba(255, 196, 25, .14), transparent 62%),
        #100821;
}

#jhm-splash *,
#jhm-splash *::before,
#jhm-splash *::after { box-sizing: border-box; }

html[data-splash="on"] #jhm-splash { opacity: 1; visibility: visible; pointer-events: auto; }

/* Halaman tidak boleh ter-scroll selama splash tampil. */
html[data-splash="on"] body { overflow: hidden; }

.jhm-stack {
    display: grid;
    justify-items: center;
    gap: 24px;
    transform: translateY(-6%);
}

/* Cincin accent berputar mengelilingi logo + halo yang berdenyut. */
.jhm-stage { position: relative; width: 132px; height: 132px; display: grid; place-items: center; }

.jhm-ring {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: conic-gradient(from 0turn, transparent 0 52%, #7c3aed 70%, #c084fc 86%, #ffc419 100%);
    -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 4px), #000 calc(100% - 3px));
    mask: radial-gradient(farthest-side, transparent calc(100% - 4px), #000 calc(100% - 3px));
    filter: drop-shadow(0 0 10px rgba(124, 58, 237, .55));
    animation: jhm-spin 1.4s linear infinite;
}

.jhm-halo {
    position: absolute;
    inset: 16px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(124, 58, 237, .40), transparent 70%);
    animation: jhm-pulse 1.4s ease-in-out infinite;
}

.jhm-logo-wrap {
    position: relative;
    width: 84px;
    height: 84px;
    border-radius: 22px;
    overflow: hidden;
    display: grid;
    place-items: center;
    background: linear-gradient(145deg, rgba(255, 255, 255, .10), rgba(255, 255, 255, .02));
    box-shadow: 0 14px 34px -12px rgba(0, 0, 0, .75), inset 0 0 0 1px rgba(255, 255, 255, .10);
}

.jhm-logo { width: 100%; height: 100%; object-fit: contain; padding: 10px; animation: jhm-pop 1.4s ease-in-out infinite; }

/* Kilau melintas sekali per siklus, lalu diam sampai putaran berikutnya. */
.jhm-logo-wrap::after {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    width: 42%;
    background: linear-gradient(100deg, transparent, rgba(255, 255, 255, .55), transparent);
    transform: translateX(-130%) skewX(-12deg);
    animation: jhm-sweep 1.4s ease-in-out infinite;
}

.jhm-copy { display: grid; justify-items: center; gap: 7px; text-align: center; }

.jhm-title {
    font-size: 1.05rem;
    font-weight: 800;
    letter-spacing: .3em;
    text-indent: .3em;
    text-transform: uppercase;
    color: var(--text, #f5f3fb);
    animation: jhm-rise .7s cubic-bezier(.2, .7, .2, 1) .12s both;
}

.jhm-sub {
    font-size: .7rem;
    font-weight: 500;
    letter-spacing: .16em;
    color: var(--text-mute, #a99cd0);
    animation: jhm-rise .7s cubic-bezier(.2, .7, .2, 1) .26s both;
}

.jhm-bar {
    position: fixed;
    left: 50%;
    bottom: calc(40px + env(safe-area-inset-bottom));
    transform: translateX(-50%);
    width: 132px;
    height: 3px;
    border-radius: 999px;
    overflow: hidden;
    background: rgba(148, 163, 184, .16);
}

.jhm-bar::after {
    content: '';
    display: block;
    width: 42%;
    height: 100%;
    border-radius: 999px;
    background: linear-gradient(90deg, #7c3aed, #c084fc, #ffc419);
    animation: jhm-slide 1.15s cubic-bezier(.4, 0, .2, 1) infinite;
}

@keyframes jhm-spin { to { transform: rotate(360deg); } }

@keyframes jhm-pulse {
    0%, 100% { opacity: .5; transform: scale(.95); }
    50% { opacity: 1; transform: scale(1.08); }
}

@keyframes jhm-pop {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.07); }
}

@keyframes jhm-sweep {
    0% { transform: translateX(-130%) skewX(-12deg); }
    55%, 100% { transform: translateX(340%) skewX(-12deg); }
}

@keyframes jhm-slide {
    0% { transform: translateX(-110%); }
    100% { transform: translateX(360%); }
}

@keyframes jhm-rise {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: none; }
}

[data-theme="light"] #jhm-splash {
    background:
        radial-gradient(120% 80% at 50% 0%, rgba(124, 58, 237, .16), transparent 62%),
        radial-gradient(90% 60% at 50% 100%, rgba(255, 196, 25, .20), transparent 62%),
        #f6f4ff;
}

[data-theme="light"] .jhm-logo-wrap {
    background: linear-gradient(145deg, #ffffff, #f1ebff);
    box-shadow: 0 14px 30px -14px rgba(76, 29, 149, .40), inset 0 0 0 1px rgba(124, 58, 237, .14);
}

[data-theme="light"] .jhm-bar { background: rgba(76, 29, 149, .10); }

@media (prefers-reduced-motion: reduce) {
    .jhm-ring, .jhm-halo, .jhm-logo, .jhm-logo-wrap::after, .jhm-bar::after { animation: none; }
    .jhm-title, .jhm-sub { animation: none; opacity: 1; transform: none; }
    .jhm-bar::after { width: 100%; transform: none; }
}
</style>
