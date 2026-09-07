<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('popup_banners', function (Blueprint $table) {
            $table->string('image_fit')->default('contain')->after('image');
            $table->string('image_position')->default('center')->after('image_fit');
        });
    }

    public function down(): void
    {
        Schema::table('popup_banners', function (Blueprint $table) {
            $table->dropColumn(['image_fit', 'image_position']);
        });
    }
};