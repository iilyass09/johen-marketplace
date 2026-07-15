<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['name' => 'QRIS', 'code' => 'qris', 'icon' => '▦'],
            ['name' => 'GoPay', 'code' => 'gopay', 'icon' => '💚'],
            ['name' => 'OVO', 'code' => 'ovo', 'icon' => '💜'],
            ['name' => 'DANA', 'code' => 'dana', 'icon' => '💙'],
            ['name' => 'ShopeePay', 'code' => 'shopeepay', 'icon' => '🧡'],
            ['name' => 'BCA', 'code' => 'bca', 'icon' => '🏛️'],
            ['name' => 'BRI', 'code' => 'bri', 'icon' => '🏦'],
            ['name' => 'Mandiri', 'code' => 'mandiri', 'icon' => '🏛️'],
            ['name' => 'BNI', 'code' => 'bni', 'icon' => '🏦'],
            ['name' => 'LinkAja', 'code' => 'linkaja', 'icon' => '❤️'],
            ['name' => 'Alfamart', 'code' => 'alfamart', 'icon' => '🔴'],
            ['name' => 'Indomaret', 'code' => 'indomaret', 'icon' => '🔵'],
        ];

        foreach ($methods as $method) {
            PaymentMethod::create($method);
        }
    }
}
