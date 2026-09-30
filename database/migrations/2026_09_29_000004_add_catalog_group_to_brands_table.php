<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tambahkan `catalog_group` ke tabel `brands`.
 *
 * Latar belakang: sinkronisasi Digiflazz mengimpor SELURUH katalog prepaid
 * mereka, sehingga tabel `products` ikut terisi produk pulsa/kuota (TELKOMSEL,
 * INDOSAT, PLN, dst) selain game. Saat lini bisnis fokus ke game, produk
 * pulsa tidak boleh diimpor ulang dan tidak boleh ikut ter-nonaktifkan oleh
 * logika "SKU sudah tidak ada di price list".
 *
 * `service_type` tidak bisa dipakai sebagai pembeda karena operator pulsa juga
 * bernilai 'topup'. Karena itu butuh kolom tersendiri.
 *
 * Nilai: 'game' (katalog game, ikut diimpor) | 'pulsa' (operator/utility).
 * Default 'game' supaya brand baru tetap ikut katalog game.
 *
 * Migration ini non-destruktif dan aman diulang: hanya menambahkan kolom bila
 * belum ada, dan menandai operator pulsa yang namanya persis sama.
 */
return new class extends Migration
{
    /**
     * Brand operator / utility. Dicocokkan case-insensitive dengan nama brand
     * hasil seed BrandSeeder dan migrasi backfill katalog.
     *
     * @var array<int, string>
     */
    private const PULSA_BRANDS = [
        'TELKOMSEL',
        'INDOSAT',
        'XL',
        'TRI',
        'AXIS',
        'SMARTFREN',
        'by.U',
        'PLN',
        'Pertamina Gas',
        'K-VISION dan GOL',
    ];

    public function up(): void
    {
        if (! Schema::hasColumn('brands', 'catalog_group')) {
            Schema::table('brands', function (Blueprint $table) {
                $table->string('catalog_group', 16)->default('game')->after('service_type');
            });
        }

        $names = array_map('mb_strtoupper', self::PULSA_BRANDS);

        DB::table('brands')
            ->whereIn(DB::raw('UPPER(name)'), $names)
            ->update(['catalog_group' => 'pulsa']);

        // Sisanya (game + brand baru)default ke 'game' dari definisi kolom.
    }

    public function down(): void
    {
        if (Schema::hasColumn('brands', 'catalog_group')) {
            Schema::table('brands', function (Blueprint $table) {
                $table->dropColumn('catalog_group');
            });
        }
    }
};
