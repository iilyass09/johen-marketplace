<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::all();
foreach ($users as $u) {
    echo $u->id . ' | ' . $u->email . ' | is_admin: ' . ($u->is_admin ? 'true' : 'false') . PHP_EOL;
}
