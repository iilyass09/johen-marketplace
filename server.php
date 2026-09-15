<?php

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$publicPath = __DIR__ . '/public';

if ($uri !== '/' && str_starts_with($uri, '/storage/')) {
    $requestedFile = $publicPath . $uri;

    if (file_exists($requestedFile)) {
        return false;
    }

    $storagePath = __DIR__ . '/storage/app/public/';
    $relativePath = substr($uri, strlen('/storage/'));

    $resolvedFile = $storagePath . $relativePath;

    if (file_exists($resolvedFile)) {
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
        ];

        $ext = strtolower(pathinfo($resolvedFile, PATHINFO_EXTENSION));
        $mime = $mimeTypes[$ext] ?? 'application/octet-stream';

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($resolvedFile));
        header('Cache-Control: public, max-age=86400');
        readfile($resolvedFile);
        exit;
    }
}

if ($uri !== '/' && is_file($publicPath . $uri)) {
    $ext = strtolower(pathinfo($publicPath . $uri, PATHINFO_EXTENSION));
    $staticMimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'mjs' => 'application/javascript',
        'json' => 'application/json',
        'map' => 'application/json',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject',
        'mp4' => 'video/mp4',
        'webm' => 'video/webm',
    ];

    header('Content-Type: ' . ($staticMimeTypes[$ext] ?? 'application/octet-stream'));
    header('Content-Length: ' . filesize($publicPath . $uri));
    header('Cache-Control: public, max-age=86400');
    readfile($publicPath . $uri);
    exit;
}

require $publicPath . '/index.php';