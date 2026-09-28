(function () {
    'use strict';

    var serviceWorkerUrl = '/service-worker.js?v=20260916-notifclick';
    var hasServiceWorker = 'serviceWorker' in navigator;
    var installPrompt = null;
    var appInstalled = false;
    var registration = null;
    var pendingWorker = null;
    var updateToastTimer = null;
    var reloading = false;
    var hadController = hasServiceWorker && Boolean(navigator.serviceWorker.controller);
    var isIos = /iPad|iPhone|iPod/.test(navigator.userAgent)
        || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    var guidePreviousFocus = null;
    var guidePreviousOverflow = null;
    var guidePopupLocked = false;
    var splashInitialized = false;

    function isStandalone() {
        return (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches)
            || window.navigator.standalone === true;
    }

    function hideSplash() {
        var splash = document.getElementById('pwaSplashScreen');
        if (!splash || splash.classList.contains('is-hiding')) return;
        splash.classList.add('is-hiding');
        window.setTimeout(function () {
            if (splash.parentNode) splash.parentNode.removeChild(splash);
        }, 240);
    }

    function initSplash() {
        if (splashInitialized || !isStandalone() || !document.body) return;
        splashInitialized = true;
        document.documentElement.classList.add('pwa-standalone');

        var splash = document.createElement('div');
        splash.id = 'pwaSplashScreen';
        splash.className = 'pwa-splash-screen';
        splash.setAttribute('aria-hidden', 'true');

        var logo = document.createElement('img');
        logo.src = '/logo.png';
        logo.alt = '';
        logo.setAttribute('aria-hidden', 'true');
        splash.appendChild(logo);
        document.body.appendChild(splash);

        if (document.readyState === 'complete') {
            hideSplash();
        } else {
            window.addEventListener('load', hideSplash, { once: true });
        }
        window.setTimeout(hideSplash, 1800);
    }

    function findTarget(node, selector) {
        while (node && node !== document) {
            if (node.matches && node.matches(selector)) return node;
            node = node.parentNode;
        }
        return null;
    }

    function installTriggers() {
        return Array.prototype.slice.call(document.querySelectorAll('[data-pwa-install-trigger]'));
    }

    function canOfferInstall() {
        if (appInstalled || isStandalone()) return false;
        if (isIos) return true;
        return Boolean(installPrompt);
    }

    function updateInstallButtons() {
        var visible = canOfferInstall();
        installTriggers().forEach(function (trigger) {
            trigger.hidden = !visible;
            trigger.classList.toggle('is-visible', visible);
            var area = trigger.parentNode;
            if (area && area.classList && area.classList.contains('popup-install-area')) {
                area.hidden = !visible;
            }
        });
    }

    function closeMobileMenu() {
        var hamburger = document.getElementById('hamburgerBtn');
        var mobileMenu = document.getElementById('mobileMenu');
        if (hamburger) hamburger.classList.remove('open');
        if (mobileMenu) mobileMenu.classList.remove('open');
    }

    function openGuide(mode) {
        var guide = document.getElementById('pwaInstallGuide');
        if (!guide || guide.classList.contains('show')) return;

        var iosContent = guide.querySelector('[data-pwa-install-ios]');
        var fallbackContent = guide.querySelector('[data-pwa-install-fallback]');
        var useIos = mode === 'ios';

        if (iosContent) iosContent.hidden = !useIos;
        if (fallbackContent) fallbackContent.hidden = useIos;

        guidePreviousFocus = document.activeElement;
        var popupOverlay = document.getElementById('popupOverlay');
        guidePopupLocked = Boolean(popupOverlay && popupOverlay.classList.contains('show'));
        if (!guidePopupLocked) {
            guidePreviousOverflow = document.body.style.overflow;
            document.body.style.overflow = 'hidden';
        }

        guide.hidden = false;
        window.setTimeout(function () {
            guide.classList.add('show');
            var closeButton = guide.querySelector('[data-pwa-install-close]');
            if (closeButton) closeButton.focus();
        }, 10);
    }

    function closeGuide() {
        var guide = document.getElementById('pwaInstallGuide');
        if (!guide || guide.hidden) return;

        guide.classList.remove('show');
        if (!guidePopupLocked && guidePreviousOverflow !== null) {
            document.body.style.overflow = guidePreviousOverflow;
        }
        guidePopupLocked = false;
        guidePreviousOverflow = null;

        if (guidePreviousFocus && !guidePreviousFocus.hidden && typeof guidePreviousFocus.focus === 'function') {
            guidePreviousFocus.focus();
        }
        guidePreviousFocus = null;

        window.setTimeout(function () {
            if (!guide.classList.contains('show')) guide.hidden = true;
        }, 240);
    }

    function install() {
        closeMobileMenu();
        if (isStandalone()) return;

        if (installPrompt) {
            var prompt = installPrompt;
            installPrompt = null;
            updateInstallButtons();
            try {
                var promptResult = prompt.prompt();
                if (promptResult && typeof promptResult.catch === 'function') {
                    promptResult.catch(function () {});
                }
            } catch (error) {
                if (isIos) openGuide('ios');
            }
            return;
        }

        if (isIos) openGuide('ios');
    }

    window.JohenPwaInstall = {
        prompt: install,
        showGuide: openGuide
    };

    if (document.body) {
        initSplash();
    } else {
        document.addEventListener('DOMContentLoaded', initSplash, { once: true });
    }

    window.addEventListener('beforeinstallprompt', function (event) {
        event.preventDefault();
        installPrompt = event;
        updateInstallButtons();
    });

    window.addEventListener('appinstalled', function () {
        installPrompt = null;
        appInstalled = true;
        updateInstallButtons();
        closeGuide();
    });

    document.addEventListener('click', function (event) {
        var trigger = findTarget(event.target, '[data-pwa-install-trigger]');
        if (trigger) {
            event.preventDefault();
            install();
            return;
        }

        if (findTarget(event.target, '[data-pwa-install-close]')) {
            event.preventDefault();
            closeGuide();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeGuide();
    });

    function skipWaiting(worker) {
        if (!worker) return;
        try {
            worker.postMessage({ type: 'SKIP_WAITING' });
        } catch (error) {}
    }

    function update(reg) {
        if (!reg || typeof reg.update !== 'function') return;
        reg.update().catch(function () {});
    }

    function applyUpdate() {
        if (updateToastTimer) {
            window.clearTimeout(updateToastTimer);
            updateToastTimer = null;
        }
        var toast = document.getElementById('pwaUpdateToast');
        if (toast && toast.parentNode) toast.parentNode.removeChild(toast);
        skipWaiting(pendingWorker);
    }

    function showUpdateToast() {
        if (!document.body || document.getElementById('pwaUpdateToast')) return;

        var toast = document.createElement('div');
        toast.id = 'pwaUpdateToast';
        toast.setAttribute('role', 'status');
        toast.style.cssText = 'position:fixed;left:50%;bottom:24px;transform:translateX(-50%) translateY(12px);'
            + 'z-index:2147483646;display:flex;gap:12px;align-items:center;padding:12px 16px;'
            + 'border-radius:12px;background:#111;color:#fff;max-width:calc(100vw - 32px);'
            + 'box-shadow:0 8px 30px rgba(0,0,0,.35);opacity:0;'
            + 'transition:opacity .25s ease,transform .25s ease;'
            + 'font:14px/1.4 system-ui,-apple-system,"Segoe UI",Roboto,sans-serif';

        var label = document.createElement('span');
        label.textContent = 'Pembaharuan tersedia';
        toast.appendChild(label);

        var button = document.createElement('button');
        button.type = 'button';
        button.textContent = 'Muat ulang';
        button.style.cssText = 'border:0;background:#fff;color:#111;white-space:nowrap;'
            + 'padding:8px 14px;border-radius:8px;cursor:pointer;'
            + 'font:600 14px/1 system-ui,-apple-system,"Segoe UI",Roboto,sans-serif';
        button.addEventListener('click', applyUpdate);
        toast.appendChild(button);

        document.body.appendChild(toast);
        window.setTimeout(function () {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(-50%) translateY(0)';
        }, 10);

        updateToastTimer = window.setTimeout(applyUpdate, 10000);
    }

    function queueUpdate(worker) {
        if (!worker) return;
        pendingWorker = worker;
        showUpdateToast();
    }

    function startUpdateLoop() {
        window.setInterval(function () { update(registration); }, 1800000);
        window.addEventListener('online', function () { update(registration); });
        window.addEventListener('pageshow', function () { update(registration); });
    }

    function register() {
        if (!hasServiceWorker) return;
        navigator.serviceWorker.register(serviceWorkerUrl, {
            scope: '/',
            updateViaCache: 'none'
        }).then(function (reg) {
            registration = reg;
            reg.addEventListener('updatefound', function () {
                var worker = reg.installing;
                if (!worker) return;
                worker.addEventListener('statechange', function () {
                    if (worker.state === 'installed' && hadController) {
                        queueUpdate(worker);
                    }
                });
            });
            if (reg.waiting && hadController) queueUpdate(reg.waiting);
            update(reg);
        }).catch(function () {});
    }

    if (hasServiceWorker) {
        navigator.serviceWorker.addEventListener('controllerchange', function () {
            if (!hadController) {
                hadController = true;
                return;
            }
            if (reloading || !isStandalone()) return;
            reloading = true;
            window.location.reload();
        });

        if (document.readyState === 'complete') {
            register();
        } else {
            window.addEventListener('load', register, { once: true });
        }

        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible') update(registration);
        });

        startUpdateLoop();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateInstallButtons, { once: true });
    } else {
        updateInstallButtons();
    }
}());
