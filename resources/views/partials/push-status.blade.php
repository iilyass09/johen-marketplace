<div id="pushStatus" class="push-status" style="display:none;">
    <i class="fas fa-bell"></i>
    <span class="push-status-text"></span>
</div>
<style>
    .push-status {
        position: fixed; right: 16px; bottom: 16px; z-index: 99999;
        display: flex; align-items: center; gap: 8px;
        padding: 8px 14px; border-radius: 999px;
        font-size: 12px; font-weight: 600; font-family: 'Inter', sans-serif;
        box-shadow: 0 8px 24px -8px rgba(0, 0, 0, .5);
        cursor: default; transition: .2s;
    }
    .push-status.active { background: rgba(16, 185, 129, .14); color: #34d399; border: 1px solid rgba(16, 185, 129, .35); }
    .push-status.idle { background: rgba(148, 163, 184, .12); color: #94a3b8; border: 1px solid rgba(148, 163, 184, .3); }
    .push-status.off { background: rgba(239, 68, 68, .12); color: #f87171; border: 1px solid rgba(239, 68, 68, .3); }
    .push-status.clickable { cursor: pointer; }
    .push-status.clickable:hover { transform: translateY(-1px); }
</style>
<script>
(function () {
    var el = document.getElementById('pushStatus');
    if (!el) return;
    var text = el.querySelector('.push-status-text');
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;

    function set(cls, txt, click) {
        el.style.display = 'flex';
        el.className = 'push-status ' + cls;
        text.textContent = txt;
        el.onclick = typeof click === 'function' ? click : null;
    }

    function check() {
        navigator.serviceWorker.getRegistration('/service-worker.js').then(function (reg) {
            if (!reg || !reg.pushManager) { set('off', 'Notifikasi tidak tersedia'); return; }
            reg.pushManager.getSubscription().then(function (sub) {
                var perm = ('Notification' in window) ? Notification.permission : 'unsupported';
                if (sub) { set('active', 'Notifikasi aktif'); }
                else if (perm === 'denied') { set('off', 'Notifikasi diblokir browser'); }
                else { set('idle', 'Nyalakan notifikasi', function () { location.reload(); }); }
            }).catch(function () { set('off', 'Notifikasi diblokir browser'); });
        }).catch(function () { set('off', 'Notifikasi tidak tersedia'); });
    }

    check();
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') check();
    });
    window.addEventListener('focus', check);
})();
</script>