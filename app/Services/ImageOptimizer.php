<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizer
{
    protected const DEFAULT_QUALITY = 86;

    protected const MIN_QUALITY = 55;

    /**
     * Simpan file upload, optimalkan (downscale + WebP) lalu kembalikan path relatif.
     */
    public static function storeOptimized(
        UploadedFile $file,
        string $dir,
        int $maxWidth = 1200,
        int $maxHeight = 1600,
        int $maxBytes = 512 * 1024
    ): string {
        $path = $file->store($dir, 'public');
        $optimized = static::optimize($path, 'public', $maxWidth, $maxHeight, $maxBytes);

        if ($optimized !== $path) {
            Storage::disk('public')->delete($path);
        }

        MediaStore::import($optimized);

        return $optimized;
    }

    /**
     * Buka sumber gambar GD dari path storage yang sudah ada (jpeg/png/webp/gif).
     */
    protected static function openPath(string $absolutePath): ?\GdImage
    {
        $img = @imagecreatefromstring((string) @file_get_contents($absolutePath));

        return $img === false ? null : $img;
    }

    /**
     * Downscale proporsional tanpa distorsi, re-encode ke WebP dengan kualitas adaptif.
     * Tidak pernah upscale dan tidak memotong gambar (keep aspect / contain).
     *
     * @return string path relatif storage (bisa .webp baru), atau path awal bila gagal
     */
    public static function optimize(
        string $diskPath,
        string $disk = 'public',
        int $maxWidth = 1200,
        int $maxHeight = 1600,
        int $maxBytes = 512 * 1024,
        ?string $outputPath = null
    ): string {
        $disk = Storage::disk($disk);
        $source = static::openPath($disk->path($diskPath));

        if ($source === null) {
            return $diskPath;
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1.0, $maxWidth / max(1, $width), $maxHeight / max(1, $height));

        if ($scale < 1.0) {
            $newWidth = max(1, (int) round($width * $scale));
            $newHeight = max(1, (int) round($height * $scale));

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($source);

            $source = $resized;
        }

        imagepalettetotruecolor($source);
        imagealphablending($source, true);
        imagesavealpha($source, true);

        $dirName = pathinfo($diskPath, PATHINFO_DIRNAME);
        $baseName = pathinfo($diskPath, PATHINFO_FILENAME);
        $webpPath = $outputPath ?? trim($dirName . '/' . $baseName . '.webp', './');
        $webpAbsolute = $disk->path($webpPath);

        $outputDir = dirname($webpAbsolute);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $inputSize = @filesize($disk->path($diskPath)) ?: 0;

        $quality = static::DEFAULT_QUALITY;
        $fallback = null;

        for (; $quality >= static::MIN_QUALITY; $quality -= 10) {
            if (!ob_start()) {
                break;
            }
            imagewebp($source, null, $quality);
            $buffer = ob_get_clean();

            if ($buffer === false) {
                continue;
            }

            $size = strlen($buffer);
            if ($size >= $inputSize) {
                continue;
            }

            if ($size <= $maxBytes) {
                file_put_contents($webpAbsolute, $buffer);
                imagedestroy($source);

                return ltrim(str_replace('\\', '/', $webpPath), '/');
            }

            if ($fallback === null || $size < $fallback[1]) {
                $fallback = [$buffer, $size];
            }
        }

        if ($fallback !== null) {
            file_put_contents($webpAbsolute, $fallback[0]);
            imagedestroy($source);

            return ltrim(str_replace('\\', '/', $webpPath), '/');
        }

        imagedestroy($source);

        return $diskPath;
    }

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
            $fallback = $file->store('brands/bg', 'public');
            MediaStore::import($fallback);

            return $fallback;
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

        $path = 'brands/bg/' . $name;
        MediaStore::import($path);

        return $path;
    }
}