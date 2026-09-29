<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Nilai bawaan identitas bisnis & kontak. Nilai yang sudah ada dan tidak
     * kosong tidak pernah ditimpa, jadi data yang disunting lewat panel admin
     * tetap utuh. `company_npwp` sengaja tidak ada di daftar ini karena NPWP
     * hanya boleh diisi dari data resmi perusahaan.
     *
     * @var array<int, array{key:string, value:string, type:string}>
     */
    private array $defaults = [
        ['key' => 'company_name', 'value' => 'PT. Johen Sukses Abadi', 'type' => 'text'],
        ['key' => 'company_address', 'value' => 'Ruko Topaz No 60, Summarecon, Bandung 40295, Jawa Barat, Indonesia', 'type' => 'text'],
        ['key' => 'contact_whatsapp', 'value' => '6282260707012', 'type' => 'text'],
        ['key' => 'contact_phone_display', 'value' => '+62 822-6070-7012', 'type' => 'text'],
        ['key' => 'contact_email', 'value' => 'corporate@johengaming.store', 'type' => 'text'],
        ['key' => 'contact_cs_email', 'value' => 'cs@johengaming.store', 'type' => 'text'],
        ['key' => 'contact_cs_hours', 'value' => 'Setiap hari 24 jam (00.00 - 23.59 WIB)', 'type' => 'text'],
    ];

    public function up(): void
    {
        $now = now();

        foreach ($this->defaults as $row) {
            $existing = DB::table('site_settings')->where('key', $row['key'])->first();

            if ($existing === null) {
                DB::table('site_settings')->insert([
                    'key' => $row['key'],
                    'value' => $row['value'],
                    'type' => $row['type'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } elseif (trim((string) $existing->value) === '') {
                // Key sudah ada tapi kosong: isi dengan nilai bawaan.
                DB::table('site_settings')
                    ->where('key', $row['key'])
                    ->update(['value' => $row['value'], 'updated_at' => $now]);
            }

            // Nilai settings di-cache 1 jam; bersihkan agar halaman langsung
            // memakai data baru tanpa menunggu cache kedaluwarsa.
            Cache::forget('setting_'.$row['key']);
        }
    }

    public function down(): void
    {
        // Sengaja tidak menghapus apa pun. Nilai ini adalah data konten yang
        // mungkin sudah disunting admin, jadi rollback tidak boleh menghapus
        // atau mengembalikan nilainya.
    }
};
