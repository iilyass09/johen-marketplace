<div id="jhm-splash" role="status" aria-label="Memuat {{ $splashName }}">
    <div class="jhm-stack">
        <div class="jhm-stage">
            <span class="jhm-halo" aria-hidden="true"></span>
            <span class="jhm-ring" aria-hidden="true"></span>
            <span class="jhm-logo-wrap">
                <img class="jhm-logo" src="{{ $themeLogo }}" alt="{{ $splashName }}" width="84" height="84" decoding="sync" fetchpriority="high">
            </span>
        </div>
        <div class="jhm-copy">
            <div class="jhm-title">{{ $splashName }}</div>
            <div class="jhm-sub">{{ $splashTagline }}</div>
        </div>
    </div>
    <div class="jhm-bar" aria-hidden="true"></div>
</div>

<script>
(function () {
    'use strict';

    var root = document.documentElement;
    if (root.getAttribute('data-splash') !== 'on') return;

    var KEY = 'jhm_splash_seen';
    var MIN_MS = 1500;
    var MAX_MS = 4000;
    var started = Date.now();
    var dismissed = false;

    function dismiss() {
        if (dismissed) return;
        dismissed = true;

        try { sessionStorage.setItem(KEY, '1'); } catch (e) { /* mode privat */ }

        // 'off' memicu transisi opacity + visibility dari CSS.
        root.setAttribute('data-splash', 'off');

        // Dilepas dari DOM setelah transisi selesai supaya tidak menambah
        // layer kompositasi di halaman yang sudah berjalan.
        window.setTimeout(function () {
            var el = document.getElementById('jhm-splash');
            if (el && el.parentNode) el.parentNode.removeChild(el);
        }, 500);
    }

    function dismissWhenReady() {
        window.setTimeout(dismiss, Math.max(0, MIN_MS - (Date.now() - started)));
    }

    // Batas atas: kalau halaman macet, jangan tahan splash selamanya.
    var hardStop = window.setTimeout(dismiss, MAX_MS);

    if (document.readyState === 'complete') {
        window.clearTimeout(hardStop);
        dismissWhenReady();
    } else {
        window.addEventListener('load', function () {
            window.clearTimeout(hardStop);
            dismissWhenReady();
        });
    }

    var overlay = document.getElementById('jhm-splash');
    if (overlay) overlay.addEventListener('click', dismiss);

    // Kalau tab di-switch lalu dikembalikan setelah splash dilewati, tetap
    // tutup supaya tidak ada overlay nyangkut di atas konten.
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden && Date.now() - started > MIN_MS) dismiss();
    });
})();
</script>
