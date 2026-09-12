<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class MediaStore
{
    /**
     * Rapikan path menjadi relatif terhadap folder public storage (mis. "products/abc.webp").
     * Menerima juga nilai lama seperti "storage/products/...", "/storage/...", atau URL lengkap.
     */
    public static function normalizePath(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $path = rawurldecode($path);
        $path = str_replace('\\', '/', $path);

        if (preg_match('#^https?://#i', $path)) {
            $path = parse_url($path, PHP_URL_PATH) ?? $path;
        }

        $path = preg_replace('#^(/)?storage/#', '', $path);
        $path = preg_replace('#^/+#', '', $path);

        return trim($path) === '' ? null : $path;
    }

    /**
     * Simpan isi file (folder public storage) ke tabel media dengan key = path relatif.
     */
    public static function import(string $path, string $disk = 'public'): bool
    {
        $path = static::normalizePath($path);

        if ($path === null) {
            return false;
        }

        $disk = Storage::disk($disk);

        if (! $disk->exists($path)) {
            return false;
        }

        $bytes = $disk->get($path);

        if ($bytes === null || $bytes === '') {
            return false;
        }

        $mime = $disk->mimeType($path) ?: static::guessMime($path);

        return static::save($path, $bytes, $mime);
    }

    /**
     * Simpan byte mentah langsung ke tabel media.
     */
    public static function importBytes(string $path, string $bytes, ?string $mime = null): bool
    {
        $path = static::normalizePath($path);

        if ($path === null || $bytes === '') {
            return false;
        }

        return static::save($path, $bytes, $mime ?: static::guessMime($path));
    }

    protected static function save(string $path, string $bytes, string $mime): bool
    {
        try {
            Media::updateOrCreate(['path' => $path], [
                'data' => base64_encode($bytes),
                'mime_type' => $mime,
            ]);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function find(string $path): ?Media
    {
        $path = static::normalizePath($path);

        return $path ? Media::where('path', $path)->first() : null;
    }

    /**
     * Kembalikan byte gambar & mime dari database, atau null bila belum tersimpan.
     *
     * @return array{data: string, mime: string, etag: string}|null
     */
    public static function bytes(string $path): ?array
    {
        $media = static::find($path);

        if (! $media || $media->data === null || $media->data === '') {
            return null;
        }

        $raw = base64_decode($media->data, true);

        if ($raw === false || $raw === '') {
            return null;
        }

        return [
            'data' => $raw,
            'mime' => $media->mime_type ?: static::guessMime($path),
            'etag' => md5($media->data),
        ];
    }

    /**
     * Hapus entri database sekaligus file fisiknya.
     */
    public static function delete(string $path, string $disk = 'public'): void
    {
        $path = static::normalizePath($path);

        if ($path === null) {
            return;
        }

        Media::where('path', $path)->delete();
        Storage::disk($disk)->delete($path);
    }

    public static function guessMime(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($ext) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            default => 'application/octet-stream',
        };
    }
}