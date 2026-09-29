<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const OLD_HOURS = 'Setiap hari 09.00 - 24.00 WIB';

    private const NEW_HOURS = 'Setiap hari 24 jam (00.00 - 23.59 WIB)';

    /**
     * Jam layanan customer service diubah menjadi 24 jam. Hanya nilai bawaan
     * lama (atau kosong) yang diganti; kalau admin sudah menyunting jam
     * sendiri, nilainya dibiarkan.
     */
    public function up(): void
    {
        $affected = DB::table('site_settings')
            ->where('key', 'contact_cs_hours')
            ->where(function ($q) {
                $q->where('value', self::OLD_HOURS)->orWhere('value', '');
            })
            ->update(['value' => self::NEW_HOURS, 'updated_at' => now()]);

        if ($affected > 0 || DB::table('site_settings')->where('key', 'contact_cs_hours')->exists()) {
            Cache::forget('setting_contact_cs_hours');
        }
    }

    public function down(): void
    {
        // Nilai konten, tidak dikembalikan agar tidak menimpa suntingan admin.
    }
};
