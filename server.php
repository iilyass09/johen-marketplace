<?php

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($uri !== '/' && str_starts_with($uri, '/storage/')) {
    $publicPath = __DIR__ . '/public';
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

return false;
