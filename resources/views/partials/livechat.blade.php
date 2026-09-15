<div id="lc-overlay" class="lc-overlay"></div>

<div id="lc-popup" class="lc-popup">
  <div class="lc-header">
    <div>
      <h3 style="font-size:13px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--purple-light,#9d5cf5);margin:0">LIVE CHAT</h3>
    </div>
    <button class="lc-header-close" onclick="window.LiveChat.close()">✕</button>
  </div>
  <div class="lc-body" id="lc-body">
    <div class="lc-empty">Memuat...</div>
  </div>
</div>

<button id="lc-fab" class="lc-fab" aria-label="Live Chat">
  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
  </svg>
  <span id="lc-fab-badge" class="lc-fab-badge" style="display:none">0</span>
</button>

<script>
window.LIVECHAT_USER = @json(auth('web')->check() ? ['id' => auth('web')->id(), 'name' => auth('web')->user()->name] : null);
</script>
<link rel="stylesheet" href="{{ asset('css/livechat.css') }}?v=31">
<script src="{{ asset('js/livechat.js') }}?v=31"></script>

<script>
(function() {
  if (!window.LIVECHAT_USER) return;
  function poll() { window.LiveChat.updateBadge(); }
  poll();
  setInterval(poll, 15000);
})();
</script>

@if (auth('web')->check() && config('services.vapid.public_key') && config('services.vapid.private_key'))
<script>
(function () {
  'use strict';
  if (!window.LIVECHAT_USER) return;
  if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;
  if ('Notification' in window && Notification.permission === 'denied') return;

  var csrf = document.querySelector('meta[name="csrf-token"]');
  if (!csrf) return;
  var token = csrf.content;
  var vapidKey = @json(config('services.vapid.public_key'));

  function b64url(value) {
    if (typeof value === 'string') return value;
    var bytes = value instanceof Uint8Array ? value : new Uint8Array(value);
    var binary = '';
    for (var i = 0; i < bytes.length; i++) binary += String.fromCharCode(bytes[i]);
    return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
  }

  function getKeys(sub) {
    if (!sub || !sub.keys || !sub.keys.p256dh || !sub.keys.auth) return null;
    return {
      public_key: b64url(sub.keys.p256dh),
      auth_token: b64url(sub.keys.auth)
    };
  }

  function persist(sub) {
    var keys = getKeys(sub);
    if (!keys) return Promise.resolve(null);
    return fetch('/api/push/subscribe', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': token,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify({
        endpoint: sub.endpoint,
        public_key: keys.public_key,
        auth_token: keys.auth_token,
      })
    }).catch(function () {});
  }

  function subscribeFresh(reg) {
    return reg.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: vapidKey
    }).then(persist);
  }

  navigator.serviceWorker.register('/service-worker.js?v=20260915-7', { updateViaCache: 'none' }).then(function (reg) {
    return reg.pushManager.getSubscription().then(function (existing) {
      if (!existing) return subscribeFresh(reg);
      if (getKeys(existing)) return persist(existing);
      return Promise.resolve(null);
    });
  }).catch(function () {});
})();
</script>
@endif
