@php
    $pushGuard = $pushGuard ?? 'admin';
    $basePath = $pushGuard === 'lcadmin' ? '/lcadmin' : '/admin';
@endphp
@include('partials.push-manager', [
    'pushGuard' => $pushGuard,
    'subscribeUrl' => $basePath . '/push/' . $pushGuard . '/subscribe',
    'testUrl' => $basePath . '/push/' . $pushGuard . '/test',
    'statusUrl' => $basePath . '/push/' . $pushGuard . '/status',
])