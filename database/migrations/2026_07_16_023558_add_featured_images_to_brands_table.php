<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->string('featured_img_1')->nullable()->after('featured_thumbnail');
            $table->string('featured_img_2')->nullable()->after('featured_img_1');
            $table->string('featured_img_3')->nullable()->after('featured_img_2');
        });
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn(['featured_img_1', 'featured_img_2', 'featured_img_3']);
        });
    }
};
