<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mematikan cache HTTP untuk halaman HTML.
 *
 * Tanpa ini, handset (terutama PWA ter-install) bisa Opening halaman dari
 * cache browser dan menampilkan versi lama padahal server sudah di-update.
 * Response ini dinamis - isi bisa berbeda tiap pengguna, tiap login, tiap harga
 * produk - jadi tidak boleh disimpan sama sekali.
 */
class NoCacheHtml
{
    /**
     * Path yang manage header sendiri dan harus tidak disentuh.
     */
    private const SKIP = [
        'service-worker.js',
        'pwa-version.json',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldBypass($request, $response)) {
            return $response;
        }

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->remove('Expires');
        $response->headers->remove('Last-Modified');

        return $response;
    }

    private function shouldBypass(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return true;
        }

        if (in_array($request->path(), self::SKIP, true)) {
            return true;
        }

        // Unduhan / export tidak boleh diubah header-nya.
        if ($response->headers->has('Content-Disposition')) {
            return true;
        }

        return false;
    }
}
