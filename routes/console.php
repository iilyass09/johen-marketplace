<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sinkronisasi rutin listing Jual Beli Akun dari johengaming.id.
// Aktif bila cron `php artisan schedule:run` dipasang di server.
Schedule::command('jba:sync-johengaming')->dailyAt('02:30');
