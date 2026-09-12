<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_listings', function (Blueprint $table) {
            $table->string('collector_tier')->nullable()->after('discount_percent');
        });
    }

    public function down(): void
    {
        Schema::table('account_listings', function (Blueprint $table) {
            $table->dropColumn('collector_tier');
        });
    }
};