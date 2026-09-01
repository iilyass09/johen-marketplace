<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ImageOptimizer
{
    /**
     * Buka sumber gambar GD dari file upload (jpeg/png/webp).
     */
    protected static function open(UploadedFile $file): ?\GdImage
    {
        $mime = $file->getMimeType();

        return match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($file->getRealPath()),
            'image/png' => @imagecreatefrompng($file->getRealPath()),
            'image/webp' => @imagecreatefromwebp($file->getRealPath()),
            default => null,
        };
    }

    /**
     * Isi area transparan dengan warna putih agar konversi ke JPG tidak menghitam.
     */
    protected static function flattenAlpha(\GdImage $img): \GdImage
    {
        $w = imagesx($img);
        $h = imagesy($img);

        $bg = imagecreatetruecolor($w, $h);
        $white = imagecolorallocate($bg, 255, 255, 255);
        imagefill($bg, 0, 0, $white);
        imagecopy($bg, $img, 0, 0, 0, 0, $w, $h);
        imagedestroy($img);

        return $bg;
    }

    /**
     * Resize & crop (pendekatan cover) lalu simpan sebagai JPG.
     *
     * @return string path relatif storage (mis. brands/bg/xxxx.jpg)
     */
    public static function optimizeAndCrop(
        UploadedFile $file,
        string $ratio,
        int $maxWidth = 1920,
        int $quality = 82
    ): string {
        $src = self::open($file);
        if (!$src) {
            return $file->store('brands/bg', 'public');
        }

        $src = self::flattenAlpha($src);

        // Parse rasio (mis. "2:1", "21:9")
        $parts = array_map('floatval', explode(':', $ratio));
        $targetRatio = ($parts[1] ?? 0) > 0 ? $parts[0] / $parts[1] : 2.0;

        $srcW = imagesx($src);
        $srcH = imagesy($src);
        $srcRatio = $srcW / max(1, $srcH);

        // step 1: resize agar lebar = maxWidth (pertahankan rasio)
        $dstW = min($maxWidth, $srcW);
        $dstH = (int) round($dstW / $srcRatio);

        $resized = imagecreatetruecolor($dstW, $dstH);
        imagecopyresampled($resized, $src, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
        imagedestroy($src);

        // step 2: crop center ke rasio target
        $cropW = $dstW;
        $cropH = (int) round($dstW / max(0.01, $targetRatio));

        if ($cropH > $dstH) {
            $cropH = $dstH;
            $cropW = (int) round($dstH * $targetRatio);
        }

        $cropW = min($cropW, $dstW);
        $cropH = min($cropH, $dstH);
        $offsetX = (int) round(($dstW - $cropW) / 2);
        $offsetY = (int) round(($dstH - $cropH) / 2);

        $canvas = imagecreatetruecolor($cropW, $cropH);
        imagecopy($canvas, $resized, 0, 0, $offsetX, $offsetY, $cropW, $cropH);
        imagedestroy($resized);

        $name = Str::random(40) . '.jpg';
        $dir = storage_path('app/public/brands/bg');

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        imagejpeg($canvas, $dir . DIRECTORY_SEPARATOR . $name, $quality);
        imagedestroy($canvas);

        return 'brands/bg/' . $name;
    }
}
