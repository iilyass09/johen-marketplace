@php
    $pushGuard = $pushGuard ?? 'admin';
    $basePath = match ($pushGuard) {
        'lcadmin' => '/lcadmin',
        'csadmin' => '/csadmin',
        default => '/admin',
    };
@endphp
@include('partials.push-manager', [
    'pushGuard' => $pushGuard,
    'subscribeUrl' => $basePath . '/push/' . $pushGuard . '/subscribe',
    'testUrl' => $basePath . '/push/' . $pushGuard . '/test',
    'statusUrl' => $basePath . '/push/' . $pushGuard . '/status',
])