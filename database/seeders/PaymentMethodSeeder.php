<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Sinkronkan daftar metode pembayaran dengan data lokal (idempotent).
     *
     * Karena aplikasi saat ini hanya benar-benar memproses pembayaran QRIS
     * (atau Invoice) Xendit, metode lain ditampilkan sebagai pilihan namun
     * real transaksinya tetap via gateway yang dikonfigurasi.
     */
    public function run(): void
    {
        $methods = [
            [
                'name' => 'GoPay',
                'code' => 'gopay',
                'category' => 'ewallet',
                'photo' => 'payments/gopay.svg',
                'photo_light' => 'payments/gopay-dark.png',
                'is_active' => false,
            ],
            [
                'name' => 'Dana',
                'code' => 'dana',
                'category' => 'ewallet',
                'photo' => 'payments/dana.svg',
                'is_active' => true,
            ],
            [
                'name' => 'LinkAja',
                'code' => 'linkaja',
                'category' => 'ewallet',
                'photo' => null,
                'photo_light' => null,
                'is_active' => true,
            ],
            [
                'name' => 'ShopeePay',
                'code' => 'shopeepay',
                'category' => 'ewallet',
                'photo' => 'payments/shopeepay.svg',
                'is_active' => false,
            ],
            [
                'name' => 'Ovo',
                'code' => 'ovo',
                'category' => 'ewallet',
                'photo' => 'payments/ovo.svg',
                'is_active' => true,
            ],
            [
                'name' => 'QRIS',
                'code' => 'qris',
                'category' => 'qris',
                'photo' => 'payments/qris.svg',
                'photo_light' => 'payments/qris-dark.png',
                'is_active' => true,
            ],
            [
                'name' => 'BCA Virtual Account',
                'code' => 'bca_va',
                'category' => 'va',
                'photo' => 'payments/bca.svg',
                'is_active' => true,
            ],
            [
                'name' => 'BRI Virtual Account',
                'code' => 'bri_va',
                'category' => 'va',
                'photo' => 'payments/bri.svg',
                'is_active' => true,
            ],
            [
                'name' => 'BNI Virtual Account',
                'code' => 'bni_va',
                'category' => 'va',
                'photo' => 'payments/bni.svg',
                'is_active' => true,
            ],
            [
                'name' => 'Mandiri Virtual Account',
                'code' => 'mandiri_va',
                'category' => 'va',
                'photo' => 'payments/mandiri.svg',
                'is_active' => true,
            ],
            [
                'name' => 'Permata Virtual Account',
                'code' => 'permata_va',
                'category' => 'va',
                'photo' => null,
                'photo_light' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Alfamart',
                'code' => 'alfamart',
                'category' => 'convenience_store',
                'photo' => 'payments/alfamart.svg',
                'is_active' => true,
            ],
            [
                'name' => 'Indomaret',
                'code' => 'indomaret',
                'category' => 'convenience_store',
                'photo' => 'payments/indomaret.svg',
                'is_active' => true,
            ],
        ];

        $seededCodes = [];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['code' => $method['code']],
                $method,
            );
            $seededCodes[] = $method['code'];
        }

        // Nonaktifkan metode yang tidak ada di daftar (agar tampilan konsisten
        // dengan lokal). Data tetap disimpan, hanya tidak ditampilkan.
        PaymentMethod::whereNotIn('code', $seededCodes)->update(['is_active' => false]);
    }
}
