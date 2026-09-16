@php
    $pushGuard = $pushGuard ?? 'web';
    $subscribeUrl = $subscribeUrl ?? null;
    $unsubscribeUrl = $unsubscribeUrl ?? null;
    $testUrl = $testUrl ?? null;
    $vapidKey = config('services.vapid.public_key');
    $vapidPrivate = config('services.vapid.private_key');
    $swUrl = '/service-worker.js?v=20260916-notifclick';
@endphp
@if ($vapidKey && $vapidPrivate)
<script>
(function () {
    'use strict';

    var GUARD = @json($pushGuard);
    var SUBSCRIBE_URL = @json($subscribeUrl);
    var TEST_URL = @json($testUrl);
    var STATUS_URL = @json($statusUrl ?? '');
    var VAPID_KEY = @json($vapidKey);
    var SW_URL = @json($swUrl);

    function getToken() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.content : '';
    }

    function b64url(value) {
        if (typeof value === 'string') return value;
        var bytes = value instanceof Uint8Array ? value : new Uint8Array(value);
        var binary = '';
        for (var i = 0; i < bytes.length; i++) binary += String.fromCharCode(bytes[i]);
        return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
    }

    function getRawKey(sub, name) {
        if (sub.getKey && typeof sub.getKey === 'function') {
            try {
                var k = sub.getKey(name);
                if (k) return k;
            } catch (e) {}
        }
        return sub.keys ? sub.keys[name] : null;
    }

    function getKeys(sub) {
        var p256dh = getRawKey(sub, 'p256dh');
        var auth = getRawKey(sub, 'auth');
        if (!sub || !sub.endpoint || !p256dh || !auth) return null;
        return {
            endpoint: sub.endpoint,
            public_key: b64url(p256dh),
            auth_token: b64url(auth)
        };
    }

    function b64urlToBytes(value) {
        var s = String(value).replace(/-/g, '+').replace(/_/g, '/');
        while (s.length % 4) s += '=';
        var bin = atob(s);
        var out = new Uint8Array(bin.length);
        for (var i = 0; i < bin.length; i++) out[i] = bin.charCodeAt(i);
        return out;
    }

    function appServerKey() {
        try {
            return b64urlToBytes(VAPID_KEY);
        } catch (e) {
            return VAPID_KEY;
        }
    }

    function persist(sub, attempt) {
        if (!SUBSCRIBE_URL) return Promise.resolve({ ok: false, status: 0, error: 'no-endpoint' });
        var keys = getKeys(sub);
        if (!keys) return Promise.resolve({ ok: false, status: 0, error: 'no-keys' });
        return fetch(SUBSCRIBE_URL, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getToken(),
                'X-XSRF-TOKEN': getToken() || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                endpoint: keys.endpoint,
                public_key: keys.public_key,
                auth_token: keys.auth_token
            })
        }).then(function (r) {
            return r.text().then(function (t) {
                var j = null;
                try { j = JSON.parse(t); } catch (e) {}
                if (!r.ok) {
                    if ((attempt || 0) < 2) {
                        return new Promise(function (resolve) {
                            setTimeout(function () { resolve(persist(sub, (attempt || 0) + 1)); }, 1200);
                        });
                    }
                    return { ok: false, status: r.status, error: (j && (j.message || j.error)) || String(t || '').slice(0, 300) };
                }
                return { ok: true, status: r.status, error: null };
            });
        }).catch(function (err) {
            return { ok: false, status: 0, error: String(err && err.message || err) };
        });
    }

    function canPush() {
        return ('serviceWorker' in navigator)
            && ('PushManager' in window)
            && ('Notification' in window)
            && typeof Notification.requestPermission === 'function';
    }

    function permissionState() {
        if (!('Notification' in window)) return 'unsupported';
        return Notification.permission;
    }

    function registerSW() {
        return navigator.serviceWorker.register(SW_URL, { updateViaCache: 'none' });
    }

    function getSubscription() {
        return registerSW().then(function (reg) {
            return reg.pushManager.getSubscription();
        });
    }

    function createSubscription() {
        return registerSW().then(function (reg) {
            return reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: appServerKey()
            });
        });
    }

    function status() {
        if (!canPush()) {
            return Promise.resolve({ ready: false, permission: permissionState(), subscribed: false, error: 'not-supported' });
        }
        return getSubscription().then(function (sub) {
            return { ready: true, permission: permissionState(), subscribed: !!sub, error: null };
        }).catch(function (err) {
            return { ready: false, permission: permissionState(), subscribed: false, error: String(err && err.message || err) };
        });
    }

    function enable() {
        if (!canPush()) return Promise.reject(new Error('Browser tidak mendukung notifikasi.'));
        return Notification.requestPermission().then(function (perm) {
            if (perm !== 'granted') {
                return { granted: false, permission: perm, subscribed: false, persisted: false, error: null };
            }
            return createSubscription().then(function (sub) {
                return persist(sub).then(function (p) {
                    if (!p.ok) {
                        console.warn('[PushBridge] gagal menyimpan subscription di server:', p);
                    }
                    return { granted: true, permission: 'granted', subscribed: true, persisted: p.ok, error: p.ok ? null : ('Server: ' + (p.error || p.status)) };
                });
            });
        });
    }

    function silentSubscribe() {
        if (!canPush() || permissionState() !== 'granted') return Promise.resolve(null);
        var sessionKey = 'jp_push_synced_' + GUARD;
        var already = false;
        try { already = sessionStorage.getItem(sessionKey) === '1'; } catch (e) {}
        return getSubscription().then(function (sub) {
            if (already) return sub;
            if (sub) {
                return persist(sub).then(function (p) {
                    try { if (p.ok) sessionStorage.setItem(sessionKey, '1'); } catch (e) {}
                    if (!p.ok) {
                        console.warn('[PushBridge] sinkronisasi subscription gagal:', p);
                        try { sessionStorage.removeItem(sessionKey); } catch (e) {}
                    }
                    return sub;
                });
            }
            return createSubscription().then(function (newSub) {
                return persist(newSub).then(function (p) {
                    try { if (p.ok) sessionStorage.setItem(sessionKey, '1'); } catch (e) {}
                    if (!p.ok) {
                        console.warn('[PushBridge] sinkronisasi subscription gagal:', p);
                        try { sessionStorage.removeItem(sessionKey); } catch (e) {}
                    }
                    return newSub;
                });
            });
        }).catch(function () { return null; });
    }

    function test(title, body) {
        if (!TEST_URL) return Promise.resolve({ ok: false, delivered: 0, error: 'no-endpoint' });
        return fetch(TEST_URL, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getToken(),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ title: title || '', body: body || '' })
        }).then(function (r) {
            return r.json().catch(function () { return {}; }).then(function (j) {
                return { ok: !!j.ok, delivered: j.delivered || 0, error: j.error || null };
            });
        }).catch(function (err) {
            return { ok: false, delivered: 0, error: String(err && err.message || err) };
        });
    }

    function statusSync() {
        if (!STATUS_URL) return Promise.resolve(null);
        return fetch(STATUS_URL, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getToken(),
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(function (r) {
            return r.json().catch(function () { return null; });
        }).then(function (j) {
            if (!j || !j.ok) return null;
            return { registered: !!j.registered, count: j.count || 0 };
        }).catch(function () { return null; });
    }

    window.PushBridge = {
        GUARD: GUARD,
        canPush: canPush,
        status: status,
        statusSync: statusSync,
        enable: enable,
        silentSubscribe: silentSubscribe,
        test: test
    };

    if (canPush() && permissionState() === 'granted') {
        silentSubscribe();
    }

    document.dispatchEvent(new CustomEvent('pushbridge:ready', { detail: { guard: GUARD } }));
})();
</script>
@endif