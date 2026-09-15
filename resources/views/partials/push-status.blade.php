<div id="pushStatus" class="push-status idle" style="display:none;" role="button" tabindex="0" aria-label="Status notifikasi push">
    <i class="fas fa-bell"></i>
    <span class="push-status-text"></span>
</div>
<style>
    .push-status {
        position: fixed; right: 16px; bottom: 16px; z-index: 99999;
        display: flex; align-items: center; gap: 10px;
        padding: 9px 16px; border-radius: 999px;
        font-size: 12px; font-weight: 600; font-family: 'Inter', 'Poppins', sans-serif;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, .6);
        cursor: default; transition: .2s;
        max-width: 420px;
        user-select: none;
    }
    .push-status.active { background: rgba(16, 185, 129, .14); color: #34d399; border: 1px solid rgba(16, 185, 129, .35); }
    .push-status.idle { background: rgba(245, 166, 35, .12); color: #fbbf24; border: 1px solid rgba(245, 166, 35, .3); }
    .push-status.off { background: rgba(239, 68, 68, .12); color: #f87171; border: 1px solid rgba(239, 68, 68, .3); }
    .push-status.clickable { cursor: pointer; }
    .push-status.clickable:hover { transform: translateY(-2px); }
    .push-status .push-test-btn, .push-status .push-enable-btn {
        border: none; cursor: pointer;
        padding: 4px 12px; border-radius: 999px;
        font-size: 11px; font-weight: 700;
        font-family: inherit;
        transition: all .15s;
        flex-shrink: 0;
    }
    .push-status .push-enable-btn { background: #fbbf24; color: #422006; }
    .push-status .push-enable-btn:hover { filter: brightness(1.08); }
    .push-status .push-test-btn { background: #10b981; color: #052e1d; }
    .push-status .push-test-btn:hover { filter: brightness(1.1); }
    .push-status .push-test-btn:disabled, .push-status .push-enable-btn:disabled { opacity: .55; cursor: wait; }
    @media (max-width: 640px) {
        .push-status { right: 10px; bottom: 76px; left: 10px; justify-content: center; max-width: none; }
    }
</style>
<script>
(function () {
    var el = document.getElementById('pushStatus');
    if (!el) return;
    var textEl = el.querySelector('.push-status-text');
    var bridge = null;
    var pending = false;
    var syncCache = { t: 0, value: null };

    function serverSync() {
        var now = Date.now();
        if (now - syncCache.t < 5000) return Promise.resolve(syncCache.value);
        return bridge.statusSync().then(function (v) {
            syncCache.t = Date.now();
            syncCache.value = v;
            return v;
        });
    }

    function esc(s) {
        return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function set(cls, html, onClick) {
        el.className = 'push-status ' + cls;
        textEl.innerHTML = html;
        el.style.display = 'flex';
        el.onclick = onClick || null;
        el.classList.toggle('clickable', !!onClick);
        el.setAttribute('aria-disabled', pending ? 'true' : 'false');
    }

    function render() {
        if (!bridge) { set('idle', 'Menyiapkan notifikasi…'); return; }
        if (pending) return;
        if (!bridge.canPush()) { set('off', 'Notifikasi tidak didukung browser ini'); return; }

        bridge.status().then(function (s) {
            if (s.permission === 'granted' && s.subscribed) {
                return serverSync().then(function (sv) {
                    if (sv && !sv.registered) {
                        set('idle', 'Terdaftar di browser, belum di server — <button type="button" class="push-enable-btn">Sinkron</button>', doEnable);
                        return;
                    }
                    set('active',
                        'Notifikasi Aktif<button type="button" class="push-test-btn">Uji</button>',
                        function (e) {
                            if (e && e.target && e.target.classList && e.target.classList.contains('push-test-btn')) { doTest(); return; }
                            doEnable();
                        });
                });
            } else if (s.permission === 'granted' && !s.subscribed) {
                set('idle', '<button type="button" class="push-enable-btn">Aktifkan Notifikasi</button>', doEnable);
            } else if (s.permission === 'denied') {
                set('off', 'Notifikasi diblokir &mdash; izinkan di pengaturan situs', null);
            } else {
                set('idle', '<button type="button" class="push-enable-btn">Aktifkan Notifikasi</button>', doEnable);
            }
        }).catch(function () { set('off', 'Notifikasi tidak tersedia', null); });
    }

    function doEnable() {
        if (pending || !bridge) return;
        pending = true;
        set('idle', 'Meminta izin&hellip;');
        bridge.enable().then(function (res) {
            pending = false;
            if (res.granted && res.subscribed && res.persisted) {
                set('active', 'Notifikasi Aktif<button type="button" class="push-test-btn">Uji</button>', function (e) {
                    if (e && e.target && e.target.classList && e.target.classList.contains('push-test-btn')) { doTest(); return; }
                    doEnable();
                });
            } else if (res.granted && res.subscribed && !res.persisted) {
                set('off', 'Terdaftar di browser tapi server menolak (' + esc(res.error || 'sinkronisasi gagal') + ') — klik untuk coba lagi', doEnable);
            } else if (res.permission === 'denied') {
                set('off', 'Notifikasi diblokir &mdash; izinkan di pengaturan situs', null);
            } else {
                set('idle', 'Izin ditunda — klik lagi untuk aktifkan', doEnable);
            }
        }).catch(function (err) {
            pending = false;
            set('off', 'Gagal mengaktifkan: ' + esc(err && err.message || err).slice(0, 90), doEnable);
        });
    }

    function doTest() {
        if (pending || !bridge) return;
        pending = true;
        set('active', 'Mengirim notifikasi uji&hellip;');
        bridge.test('Johen', 'Ini notifikasi uji — push berfungsi!')
            .then(function (res) {
                pending = false;
                if (res.ok && res.delivered > 0) { set('active', 'Dikirim ke ' + res.delivered + ' perangkat ✓'); }
                else if (res.ok) { set('active', 'Belum ada perangkat terdaftar'); }
                else { set('active', 'Gagal kirim uji'); }
                setTimeout(render, 2500);
            })
            .catch(function () { pending = false; set('active', 'Gagal kirim uji'); setTimeout(render, 2500); });
    }

    function wire() {
        if (window.PushBridge) bridge = window.PushBridge;
        render();
    }

    document.addEventListener('DOMContentLoaded', wire);
    wire();
    var t = setInterval(wire, 2500);
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') wire();
    });
    window.addEventListener('focus', wire);
    window.addEventListener('beforeunload', function () { clearInterval(t); });
})();
</script>