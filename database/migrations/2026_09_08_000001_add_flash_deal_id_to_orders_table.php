<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('flash_deal_id')->nullable()->after('category')->constrained()->nullOnDelete();
            $table->decimal('original_price', 12, 2)->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('flash_deal_id');
            $table->dropColumn('original_price');
        });
    }
};
