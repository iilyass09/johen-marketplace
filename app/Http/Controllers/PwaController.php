<?php

namespace App\Http\Controllers;

use App\Services\PwaBuildService;
use Illuminate\Http\Response;

/**
 * Melayani service worker sebagai script yang di-generate per request.
 *
 * Alasannya bukan sekadar wants: file service worker harus berubah byte-nya
 * setiap deploy. Kalau tidak, browser membandingkan script dengan worker yang
 * terpasang, mendapati identik, dan tidak memasang worker baru - aset hasil
 * git pull lalu tetap dilayani dari cache lama.
 */
class PwaController extends Controller
{
    public const SW_PATH = 'resources/service-worker.js';

    public function serviceWorker(): Response
    {
        $template = base_path(self::SW_PATH);

        if (! is_file($template)) {
            abort(404);
        }

        $script = str_replace(
            '__JOHEN_BUILD__',
            PwaBuildService::stamp(),
            (string) file_get_contents($template)
        );

        return response($script, 200, [
            'Content-Type' => 'application/javascript; charset=utf-8',
            // Wajib: supaya browser selalu mengecek ulang worker ini.
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Service-Worker-Allowed' => '/',
        ]);
    }

    public function version()
    {
        return response()->json([
            'ok' => true,
            'build' => PwaBuildService::stamp(),
        ]);
    }
}
