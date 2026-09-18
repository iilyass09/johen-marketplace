<?php

namespace App\Services;

use App\Models\LiveChatMessage;
use Illuminate\Http\UploadedFile;

class LiveChatMedia
{
    public const STORAGE_DIR = 'live-chat/media';

    /**
     * Simpan batch file multipart (media[]) sebagai satu grup attachment.
     *
     * @param  \Illuminate\Http\UploadedFile[]  $files
     * @param  array<int, \Illuminate\Http\UploadedFile|null>  $posterFiles  dipetakan berdasarkan index media
     * @param  array<int, int|null>  $durations  durasi dalam detik, dipetakan berdasarkan index media (untuk audio)
     */
    public static function storeBatch(array $files, array $posterFiles = [], array $durations = [], bool $importToStore = true): array
    {
        $rows = [];

        foreach (array_values($files) as $index => $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store(self::STORAGE_DIR, 'public');

            // Audio berdurasi panjang tidak diimpor ke tabel media (base64 DB) agar tidak membebani
            // memori/packet MySQL. File disajikan langsung dari disk (public) via /media/{path}.
            if ($importToStore) {
                MediaStore::import($path);
            }

            $row = [
                'media_path' => $path,
                'media_name' => $file->getClientOriginalName(),
                'media_mime' => $file->getMimeType(),
                'media_size' => $file->getSize(),
                'media_duration' => $durations[$index] ?? null,
                'poster_path' => null,
            ];

            $poster = $posterFiles[$index] ?? null;
            if ($poster instanceof UploadedFile) {
                $posterPath = $poster->store(self::STORAGE_DIR, 'public');
                MediaStore::import($posterPath);
                $row['poster_path'] = $posterPath;
            }

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Isi kolom legacy satu-media (media_path/poster_path/dst) dari attachment pertama
     * agar kode lama (quote preview, dsb.) tetap berfungsi.
     */
    public static function applyCompatColumns(LiveChatMessage $message, array $rows): void
    {
        $first = $rows[0] ?? null;
        if (!$first) {
            return;
        }

        $message->forceFill([
            'media_path' => $first['media_path'],
            'media_name' => $first['media_name'],
            'media_mime' => $first['media_mime'],
            'media_size' => $first['media_size'],
            'media_duration' => $first['media_duration'],
            'poster_path' => $first['poster_path'],
        ])->save();
    }
}