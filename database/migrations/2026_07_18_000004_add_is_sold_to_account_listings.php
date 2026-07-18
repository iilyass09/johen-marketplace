<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_listings', function (Blueprint $table) {
            $table->boolean('is_sold')->default(false)->after('discount_percent');
        });
    }

    public function down(): void
    {
        Schema::table('account_listings', function (Blueprint $table) {
            $table->dropColumn('is_sold');
        });
    }
};
