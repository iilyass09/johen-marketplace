<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Stamp build untuk PWA.
 *
 * Dipakai sebagai cache-buster service worker: begitu ada file publik yang
 * berubah (deploy / git pull), stamp ikut berubah, sehingga script service
 * worker yang dilayani berubah byte-nya, browser memasang worker baru, dan
 * aset lama dibuang dari cache.
 */
class PwaBuildService
{
    public const CACHE_KEY = 'pwa.build.stamp';
    public const CACHE_TTL = 60;

    /**
     * File & folder publik yang isinya dicache service worker. Kalau salah
     * satu berubah, versi PWA ikut naik.
     */
    public static function trackedPaths(): array
    {
        return [
            base_path('resources/service-worker.js'),
            public_path('site.webmanifest'),
            public_path('css'),
            public_path('js'),
            public_path('img'),
        ];
    }

    /**
     * Mtime terakhir dari semua file yang dilacak.
     */
    public static function lastModified(): int
    {
        $newest = 0;

        foreach (self::trackedPaths() as $path) {
            if (is_file($path)) {
                $newest = max($newest, (int) filemtime($path));

                continue;
            }

            if (! is_dir($path)) {
                continue;
            }

            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($files as $file) {
                if ($file->isFile()) {
                    $newest = max($newest, (int) $file->getMTime());
                }
            }
        }

        return $newest;
    }

    /**
     * Stamp singkat (8 karakter hex) yang stabil selama tidak ada perubahan.
     */
    public static function stamp(): string
    {
        $stamp = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return substr(hash('sha256', (string) self::lastModified()), 0, 8);
        });

        return (string) $stamp;
    }
}
