<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Brand/game yang menggunakan Zone ID / Server ID pada akunnya.
     */
    public const ZONE_BRANDS = [
        'Mobile Legends',
        'Mobile Legends: Bang Bang',
        'Mobile Legends Bang Bang',
        'Genshin Impact',
        'Honkai Impact 3rd',
        'Honkai: Star Rail',
        'Honkai Star Rail',
        'Zenless Zone Zero',
        'Honor of Kings',
        'Arena of Valor',
        'Arena of Valor (AOV)',
        'AOV',
        'Tower of Fantasy',
        'Farlight 84',
    ];

    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->boolean('requires_zone_id')->default(false)->after('service_type');
        });

        foreach (self::ZONE_BRANDS as $name) {
            \DB::table('brands')
                ->where(\DB::raw('LOWER(name)'), strtolower($name))
                ->update(['requires_zone_id' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('requires_zone_id');
        });
    }
};
