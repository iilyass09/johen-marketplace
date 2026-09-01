<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('gateway_type')->nullable()->default('invoice')->after('gateway_invoice_url');
            $table->text('qr_string')->nullable()->after('gateway_type');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['gateway_type', 'qr_string']);
        });
    }
};
