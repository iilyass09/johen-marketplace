const APP_NAME = 'Johen Gaming';

function normalizePayload(raw) {
  const nested = raw && raw.data && typeof raw.data === 'object' ? raw.data : {};
  const data = { ...raw, ...nested };
  const conversationId = data.conversation_id || data.conversationId || null;
  const channelSlug = data.channel_slug || data.channelSlug || null;
  let url = data.url || data.target_url || '';

  if (!url && conversationId) {
    if (data.target_guard === 'lcadmin') url = `/lcadmin/conversations?open=${conversationId}`;
    else if (data.target_guard === 'admin') url = `/admin/live-chat/conversations/${conversationId}`;
    else if (channelSlug) url = `/?chat=1&channel=${encodeURIComponent(channelSlug)}`;
  }

  return {
    ...data,
    title: String(data.title || data.sender_name || data.senderName || '').trim(),
    body: String(data.body || data.message || data.preview || '').trim(),
    url,
    conversation_id: conversationId,
    channel_slug: channelSlug,
  };
}

function safeIcon(value) {
  if (value && typeof value === 'string' && /^https?:\/\//i.test(value)) return value;
  return '/logo-96.png';
}

function buildNotification(data) {
  const conversationId = data.conversation_id || data.conversationId || null;
  const opts = {
    body: data.body,
    icon: safeIcon(data.icon),
    badge: safeIcon(data.badge || '/logo-96.png'),
    lang: 'id-ID',
    dir: 'auto',
    silent: false,
    requireInteraction: false,
    timestamp: Date.now(),
    data: {
      appName: data.app_name || APP_NAME,
      url: data.url,
      msgId: data.msg_id || data.msgId || null,
      conversationId,
      channelSlug: data.channel_slug || data.channelSlug || null,
      targetGuard: data.target_guard || data.targetGuard || null,
    },
  };
  if (data.tag) opts.tag = String(data.tag);
  else if (conversationId) opts.tag = 'conv-' + conversationId;
  return opts;
}

async function fetchLatestContent() {
  try {
    const res = await fetch('/api/push/latest-message', {
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json' },
      cache: 'no-store',
    });
    if (!res.ok) return null;
    const json = await res.json();
    return json && typeof json === 'object' ? json : null;
  } catch (e) {
    return null;
  }
}

async function maybeShow(data) {
  let normalized = normalizePayload(data);
  if (!normalized.title || !normalized.body) {
    const latest = await fetchLatestContent();
    if (latest) normalized = normalizePayload(latest);
  }
  const title = normalized.title || 'Pesan Baru';
  const body = normalized.body || 'Kamu menerima pesan baru.';
  const url = normalized.url || '/';
  await self.registration.showNotification(title, buildNotification({ ...normalized, body, url }));
}

self.addEventListener('install', () => {
  // Tanpa skipWaiting(). Service worker baru hanya aktif saat navigasi berikutnya,
  // sehingga Chrome tidak menampilkan notifikasi update bawaan.
});

self.addEventListener('activate', (event) => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('message', (event) => {
  const msg = event.data || {};
  if (msg.type !== 'chat-notify') return;
  const data = normalizePayload(msg.data || {});
  if (!data.title || !data.body || !data.url) return;
  event.waitUntil(maybeShow(data).catch((error) => {
    console.error('Johen Gaming notifikasi chat gagal:', error);
  }));
});

self.addEventListener('push', (event) => {
  let data = {};
  try {
    const parsed = event.data ? event.data.json() : null;
    if (parsed && typeof parsed === 'object') data = parsed;
  } catch (e) {
    try {
      const text = event.data ? event.data.text() : '';
      data = text ? { body: text } : {};
    } catch (e2) {
      data = {};
    }
  }

  const handle = maybeShow(data).catch((error) => {
    console.error('Johen Gaming push notification gagal:', error);
    return self.registration.showNotification('Pesan Baru', {
      body: 'Kamu menerima pesan baru.',
      icon: '/logo-96.png',
      badge: '/logo-96.png',
    });
  });

  event.waitUntil(handle);
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  const data = event.notification.data || {};
  const raw = data.url;
  let isAdminNotif = data.targetGuard === 'admin' || data.targetGuard === 'lcadmin';

  let target;
  try {
    target = new URL(raw, self.location.href);
  } catch (e) {
    target = new URL('/', self.location.href);
  }

  // Fallback deteksi notifikasi admin via pola URL untuk notifikasi lama.
  if (!isAdminNotif) {
    const path = target.pathname || '';
    if (path === '/admin/' || path.startsWith('/admin/') || path === '/lcadmin/' || path.startsWith('/lcadmin/')) {
      isAdminNotif = true;
    }
  }

  event.waitUntil((async () => {
    const windows = await self.clients.matchAll({ type: 'window', includeUncontrolled: true });
    for (const client of windows) {
      try {
        if (new URL(client.url).origin === target.origin) {
          await client.navigate(target.href);
          return client.focus();
        }
      } catch (e) { /* lewati client yang gagal dinavigasi */ }
    }
    // Belum ada jendela web terbuka: admin/lcadmin diarahkan ke halaman login,
    // bukan ke beranda.
    const openUrl = isAdminNotif ? '/admin/login' : target.href;
    return self.clients.openWindow(openUrl);
  })());
});