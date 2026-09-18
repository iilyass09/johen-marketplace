<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminCsSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'csadmin'],
            [
                'name' => 'Admin CS',
                'email' => 'cs@johen.com',
                'password' => Hash::make('csadmin'),
                'is_admin' => false,
                'is_live_chat_cs' => true,
            ]
        );
    }
}
