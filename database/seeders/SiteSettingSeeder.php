<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'Johen Gaming', 'type' => 'text'],
            ['key' => 'site_tagline', 'value' => 'Top Up & Joki Game Termurah', 'type' => 'text'],
            ['key' => 'site_description', 'value' => 'Top up game & voucher terlaris, murah, aman legal 100% buka 24 jam dengan payment terlengkap Indonesia.', 'type' => 'textarea'],
            ['key' => 'site_logo', 'value' => '', 'type' => 'image'],
            ['key' => 'hero_title_1', 'value' => 'JASA JOKI', 'type' => 'text'],
            ['key' => 'hero_title_2', 'value' => 'TOP UP DIAMOND', 'type' => 'text'],
            ['key' => 'hero_title_3', 'value' => 'EVENT SPESIAL', 'type' => 'text'],
            ['key' => 'contact_email', 'value' => '', 'type' => 'text'],
            ['key' => 'contact_whatsapp', 'value' => '', 'type' => 'text'],
            ['key' => 'contact_instagram', 'value' => '', 'type' => 'text'],
            ['key' => 'footer_text', 'value' => '© ' . date('Y') . ' Johen Gaming. All Rights Reserved.', 'type' => 'text'],
            ['key' => 'min_balance_alert', 'value' => '0', 'type' => 'number'],
        ];

        foreach ($settings as $s) {
            SiteSetting::set($s['key'], $s['value'], $s['type']);
        }
    }
}
