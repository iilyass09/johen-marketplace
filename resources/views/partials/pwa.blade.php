<meta name="theme-color" content="#000000">
<meta name="application-name" content="APP Johen gaming">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="APP Johen gaming">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo-180.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('logo-192.png') }}">
<style>
.pwa-splash-screen{position:fixed;inset:0;z-index:2147483647;display:none;align-items:center;justify-content:center;padding:24px;background:#000;overflow:hidden;}
html.pwa-standalone .pwa-splash-screen{display:flex;}
.pwa-splash-screen.is-hiding{opacity:0;visibility:hidden;transition:opacity .2s ease,visibility .2s ease;}
.pwa-splash-screen img{width:min(34vw,180px);height:auto;max-width:min(42vw,220px);max-height:42vh;object-fit:contain;}
@media (max-width:480px){.pwa-splash-screen img{width:min(42vw,160px);max-width:160px;}}
</style>
<script src="{{ asset('js/pwa-register.js') }}?v=3" defer></script>
