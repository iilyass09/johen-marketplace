<?php

namespace App\Http\Controllers;

use App\Services\MediaStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function show(Request $request, string $path)
    {
        $path = MediaStore::normalizePath($path);

        if ($path === null) {
            abort(404);
        }

        if ($media = MediaStore::bytes($path)) {
            return response($media['data'], 200, [
                'Content-Type' => $media['mime'],
                'Content-Length' => (string) strlen($media['data']),
                'Cache-Control' => 'public, max-age=31536000, immutable',
                'X-Content-Type-Options' => 'nosniff',
            ])->setEtag($media['etag']);
        }

        if (Storage::disk('public')->exists($path)) {
            // BinaryFileResponse mendukung HTTP Range (seek audio/video) dan streaming per-chunk.
            return response()
                ->file(Storage::disk('public')->path($path), [
                    'Cache-Control' => 'public, max-age=31536000, immutable',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
        }

        abort(404);
    }
}