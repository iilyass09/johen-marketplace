<?php

namespace App\Console\Commands;

use App\Services\DigiflazzService;
use Illuminate\Console\Command;

class SyncDigiflazzProducts extends Command
{
    protected $signature = 'digiflazz:sync';
    protected $description = 'Sync products from Digiflazz price list';

    public function handle(DigiflazzService $digiflazz): void
    {
        $this->info('Fetching products from Digiflazz...');

        $result = $digiflazz->syncProducts();

        if ($result['success']) {
            $this->info($result['message']);
        } else {
            $this->error($result['message']);
        }
    }
}
