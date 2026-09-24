<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_orders', function (Blueprint $table) {
            $table->string('gateway_invoice_url', 500)->nullable()->after('gateway_invoice_id');
            $table->string('va_number', 191)->nullable()->after('qr_string');
            $table->string('payment_code', 191)->nullable()->after('va_number');
            $table->string('checkout_url', 500)->nullable()->after('payment_code');
            $table->json('gateway_extra')->nullable()->after('checkout_url');
        });
    }

    public function down(): void
    {
        Schema::table('account_orders', function (Blueprint $table) {
            $table->dropColumn([
                'gateway_invoice_url',
                'va_number',
                'payment_code',
                'checkout_url',
                'gateway_extra',
            ]);
        });
    }
};
