<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_listings', function (Blueprint $table) {
            $table->string('deal_type')->default('normal')->after('collector_tier');
        });
    }

    public function down(): void
    {
        Schema::table('account_listings', function (Blueprint $table) {
            $table->dropColumn('deal_type');
        });
    }
};