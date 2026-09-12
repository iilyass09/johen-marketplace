<?php

if (! function_exists('media_url')) {
    /**
     * URL untuk gambar yang tersimpan di database (tabel media).
     * Menerima path relatif storage, path ber-prefix "storage/", maupun URL lengkap.
     */
    function media_url($path = null)
    {
        if ($path === null || $path === '') {
            return null;
        }

        $path = (string) $path;

        if (preg_match('#^https?://#i', $path)) {
            $path = parse_url($path, PHP_URL_PATH) ?? $path;
            $path = preg_replace('#^(/)?storage/#', '', $path);
        } else {
            $path = preg_replace('#^(/)?storage/#', '', $path);
        }

        $path = ltrim($path, '/');

        if ($path === '') {
            return null;
        }

        return url('media/'.$path);
    }
}