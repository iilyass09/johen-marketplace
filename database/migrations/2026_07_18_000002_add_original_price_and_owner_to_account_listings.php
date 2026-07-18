<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_listings', function (Blueprint $table) {
            $table->decimal('original_price', 12, 2)->nullable()->after('price');
            $table->string('owner_name')->nullable()->after('specifications');
        });
    }

    public function down(): void
    {
        Schema::table('account_listings', function (Blueprint $table) {
            $table->dropColumn(['original_price', 'owner_name']);
        });
    }
};
