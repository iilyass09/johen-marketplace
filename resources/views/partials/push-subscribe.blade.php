@php($pushGuard = $pushGuard ?? 'admin')
@if (config('services.vapid.public_key') && config('services.vapid.private_key'))
<script>
(function () {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;
    if ('Notification' in window && Notification.permission === 'denied') return;

    var guard = @json($pushGuard);
    var basePath = guard === 'lcadmin' ? '/lcadmin' : '/admin';
    var subscribeUrl = basePath + '/push/' + guard + '/subscribe';
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
        return fetch(subscribeUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                endpoint: sub.endpoint,
                public_key: keys.public_key,
                auth_token: keys.auth_token
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