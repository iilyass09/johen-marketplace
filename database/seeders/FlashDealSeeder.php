<?php

namespace Database\Seeders;

use App\Models\FlashDeal;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Seeder contoh flash deal untuk pengembangan/demo.
 * Jalankan manual: php artisan db:seed --class=FlashDealSeeder
 */
class FlashDealSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $picks = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('brand')
            ->orderBy('selling_price');

        $products = $picks->take(6)->get();

        if ($products->isEmpty()) {
            $this->command?->warn('Tidak ada produk aktif. Lewati seeding flash deal.');

            return;
        }

        foreach ($products as $i => $product) {
            if ($i === 0) {
                $startsAt = $now->copy()->subHours(1);
                $hours = 24;
            } elseif ($i === 1) {
                $startsAt = $now->copy()->subMinutes(10);
                $hours = 12;
            } else {
                $startsAt = $now->copy()->addHours(2);
                $hours = 8;
            }

            FlashDeal::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'discount_percent' => [10, 15, 20, 8, 12, 18][$i % 6],
                    'stock' => 20,
                    'starts_at' => $startsAt,
                    'ends_at' => $startsAt->copy()->addHours($hours),
                    'is_active' => true,
                ]
            );
        }

        $this->command?->info('Flash deal contoh berhasil dibuat.');
    }
}
