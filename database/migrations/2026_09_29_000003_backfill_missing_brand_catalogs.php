<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Sinkronkan tabel `brands` dengan nama brand yang muncul di katalog produk.
 *
 * Masalah: produk hasil sinkronisasi Digiflazz memakai nama operator sebagai
 * `products.brand` (TELKOMSEL, INDOSAT, PLN, dst). Internationale itu tidak
 * punya baris di tabel `brands`, sehingga route /games/{brand} membinding model
 * Brand dan menghasilkan 404. Akibatnya produknya ada di database tapi tidak
 * bisa dibuka pengunjung maupun crawler sama sekali.
 *
 * Migration ini membuat baris brand yang hilang saja. Baris yang sudah ada
 * tidak disentuh, jadi editan manual admin tetap aman dan migration aman
 * dijalankan berulang kali.
 */
return new class extends Migration
{
    public function up(): void
    {
        $catalogs = $this->catalogs();

        $existing = DB::table('brands')
            ->pluck('name')
            ->map(fn ($name) => mb_strtoupper(trim((string) $name)))
            ->all();

        $maxSort = (int) (DB::table('brands')->max('sort_order') ?? 0);
        $sort = $maxSort;

        foreach ($catalogs as $name => $meta) {
            $key = mb_strtoupper(trim($name));

            if (in_array($key, $existing, true)) {
                continue;
            }

            $sort += 1;

            DB::table('brands')->insert([
                'name' => $name,
                'icon' => $meta['icon'],
                'category' => $meta['category'],
                'service_type' => 'topup',
                'requires_zone_id' => $meta['zone'] ? 1 : 0,
                'description' => $meta['description'],
                'is_active' => 1,
                // Katalog operator tidak masuk kartu bento, hanya daftar game
                // "PRODUK LAINNYA" supaya tampilan beranda tidak berubah.
                'is_popular' => 0,
                'sort_order' => $sort,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $existing[] = $key;
        }
    }

    public function down(): void
    {
        // Sengaja tidak menghapus apa pun. Baris brand hasil migration ini
        // sudah pointing ke produk aktif, dan baris dengan nama yang sama
        // bisa saja sudah ada sebelumnya sehingga tidak jelas mana yang aman
        // dihapus. Hapus manual lewat panel admin bila memang diperlukan.
    }

    /**
     * @return array<string, array{icon: string, category: string, zone: bool, description: string}>
     */
    private function catalogs(): array
    {
        return [
            'TELKOMSEL' => [
                'icon' => '📶',
                'category' => 'Telkomsel',
                'zone' => false,
                'description' => 'Top up pulsa, paket data, dan Telkomsel Unlimited dengan proses instan dan pembayaran lengkap.',
            ],
            'INDOSAT' => [
                'icon' => '📶',
                'category' => 'Indosat',
                'zone' => false,
                'description' => 'Isi ulang pulsa dan paket data Indosat, termasuk Freedom Internet dan Yellow, langsung masuk ke nomor kamu.',
            ],
            'XL' => [
                'icon' => '📶',
                'category' => 'XL Axiata',
                'zone' => false,
                'description' => 'Top up pulsa dan paket data XL Axiata seperti Hotrod Special dan Xtra Combo Flex dengan transaksi instan.',
            ],
            'TRI' => [
                'icon' => '📶',
                'category' => 'Telkomsel',
                'zone' => false,
                'description' => 'Paket data Telkomsel Happy untuk jaringan 3.5G dan 4G LTE,isi saldo dan paket dalam hitungan detik.',
            ],
            'AXIS' => [
                'icon' => '📶',
                'category' => 'Axis Networks',
                'zone' => false,
                'description' => 'Top up pulsa dan paket data Axis untuk region Jawa Bali Nusra maupun seluruh Indonesia.',
            ],
            'SMARTFREN' => [
                'icon' => '📶',
                'category' => 'Smartfren',
                'zone' => false,
                'description' => 'Isi pulsa, paket harian, dan Smartfren Unlimited dengan proses otomatis dan status pesanan bisa dicek.',
            ],
            'by.U' => [
                'icon' => '📶',
                'category' => 'by.U',
                'zone' => false,
                'description' => 'Top up pulsa by.U untuk region Jawa Bali Nusra, langsung masuk tanpa registrasi ulang.',
            ],
            'PLN' => [
                'icon' => '⚡',
                'category' => 'PLN',
                'zone' => false,
                'description' => 'Pembelian token dan pulsa listrik PLN, pembayaran aman dan token dikirim otomatis setelah transaksi berhasil.',
            ],
            'Pertamina Gas' => [
                'icon' => '⛽',
                'category' => 'Pertamina Gas',
                'zone' => false,
                'description' => 'Top up saldoPertamina Gas untuk pembelian gasifier dan tabung gas, pembayaran instan.',
            ],
            'K-VISION dan GOL' => [
                'icon' => '📺',
                'category' => 'K-Vision',
                'zone' => false,
                'description' => 'Tagihan K-Vision dan GOL, pembayaran tanpa kode prospectus dan penyelesaian otomatis.',
            ],
            'Moontoon' => [
                'icon' => '🗡️',
                'category' => 'Moonton',
                'zone' => true,
                'description' => 'Top up diamond Mobile Legends secara instan. Masukkan User ID dan Zone ID, diamond langsung masuk ke akun kamu.',
            ],
        ];
    }
};
