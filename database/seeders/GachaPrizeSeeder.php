<?php

namespace Database\Seeders;

use App\Models\GachaPrize;
use Illuminate\Database\Seeder;

class GachaPrizeSeeder extends Seeder
{
    /**
     * Sektor roda gacha bawaan. Bobot = peluang keluar, sekaligus besar sudut
     * sektor di roda, jadi tampilan dan peluang sebenarnya selalu sama.
     */
    public function run(): void
    {
        $prizes = [
            [
                'label' => 'Diskon 5%',
                'discount_type' => 'percent',
                'discount_value' => 5,
                'min_spend' => 10000,
                'weight' => 50,
                'color' => '#7c3aed',
                'validity_days' => 14,
            ],
            [
                'label' => 'Diskon 7%',
                'discount_type' => 'percent',
                'discount_value' => 7,
                'min_spend' => 25000,
                'weight' => 30,
                'color' => '#8b5cf6',
                'validity_days' => 14,
            ],
            [
                'label' => 'Diskon 10%',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'min_spend' => 50000,
                'weight' => 15,
                'color' => '#a855f7',
                'validity_days' => 21,
            ],
            [
                'label' => 'Diskon 20%',
                'discount_type' => 'percent',
                'discount_value' => 20,
                'min_spend' => 100000,
                'weight' => 5,
                'color' => '#ec4899',
                'validity_days' => 30,
            ],
        ];

        foreach ($prizes as $index => $prize) {
            GachaPrize::updateOrCreate(
                ['label' => $prize['label']],
                $prize + [
                    'quota' => null,
                    'is_active' => true,
                    'sort_order' => $index,
                ]
            );
        }
    }
}
