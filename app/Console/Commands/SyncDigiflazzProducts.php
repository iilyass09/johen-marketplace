<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\DigiflazzService;
use Illuminate\Console\Command;

class SyncDigiflazzProducts extends Command
{
    protected $signature = 'digiflazz:sync';
    protected $description = 'Sync products from Digiflazz price list';

    public function handle(DigiflazzService $digiflazz): void
    {
        $this->info('Fetching products from Digiflazz...');

        $data = $digiflazz->getPriceList();

        if (empty($data)) {
            $this->error('Failed to fetch data from Digiflazz');
            return;
        }

        $count = 0;
        foreach ($data as $item) {
            Product::updateOrCreate(
                ['buyer_sku_code' => $item['buyer_sku_code']],
                [
                    'brand' => $item['brand'],
                    'category' => $item['category'],
                    'product_name' => $item['product_name'],
                    'price' => $item['price'],
                    'selling_price' => $item['price'] + ($item['price'] * 0.05),
                    'type' => $item['type'],
                    'is_active' => $item['buyer_product_status'] === true,
                ]
            );
            $count++;
        }

        $this->info("Successfully synced {$count} products");
    }
}
