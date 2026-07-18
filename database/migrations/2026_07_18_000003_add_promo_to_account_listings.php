<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_listings', function (Blueprint $table) {
            $table->string('promo_type')->default('none')->after('is_active');
            $table->integer('discount_percent')->nullable()->after('promo_type');
        });
    }

    public function down(): void
    {
        Schema::table('account_listings', function (Blueprint $table) {
            $table->dropColumn(['promo_type', 'discount_percent']);
        });
    }
};
