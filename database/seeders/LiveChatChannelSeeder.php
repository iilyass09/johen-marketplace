<?php

namespace Database\Seeders;

use App\Models\LiveChatChannel;
use Illuminate\Database\Seeder;

class LiveChatChannelSeeder extends Seeder
{
    public function run(): void
    {
        $channels = [
            ['name' => 'Johen PUBG', 'slug' => 'johen-pubg', 'description' => 'Live chat untuk game PUBG Mobile', 'sort_order' => 1],
            ['name' => 'Johen MLBB', 'slug' => 'johen-mlbb', 'description' => 'Live chat untuk game Mobile Legends', 'sort_order' => 2],
            ['name' => 'Johen Roblox', 'slug' => 'johen-roblox', 'description' => 'Live chat untuk game Roblox', 'sort_order' => 3],
            ['name' => 'Johen E-Football', 'slug' => 'johen-e-football', 'description' => 'Live chat untuk game eFootball', 'sort_order' => 4],
            ['name' => 'Johen Free Fire', 'slug' => 'johen-free-fire', 'description' => 'Live chat untuk game Free Fire', 'sort_order' => 5],
            ['name' => 'Johen FC Mobile', 'slug' => 'johen-fc-mobile', 'description' => 'Live chat untuk game FC Mobile', 'sort_order' => 6],
            ['name' => 'Johen Valorant', 'slug' => 'johen-valorant', 'description' => 'Live chat untuk game Valorant', 'sort_order' => 7],
            ['name' => 'Monkey PUBG', 'slug' => 'monkey-pubg', 'description' => 'Live chat untuk Monkey PUBG', 'sort_order' => 8],
            ['name' => 'Johen CS', 'slug' => \App\Models\LiveChatChannel::CS_SLUG, 'description' => 'Live chat Admin CS untuk pengunjung (guest)', 'sort_order' => 99],
        ];

        foreach ($channels as $channel) {
            LiveChatChannel::updateOrCreate(
                ['slug' => $channel['slug']],
                $channel
            );
        }
    }
}
