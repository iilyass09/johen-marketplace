<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('gateway_invoice_url');
            $table->string('va_number')->nullable()->after('qr_string');
            $table->string('payment_code')->nullable()->after('va_number');
            $table->text('checkout_url')->nullable()->after('payment_code');
            $table->json('gateway_extra')->nullable()->after('checkout_url');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'va_number', 'payment_code', 'checkout_url', 'gateway_extra']);
        });
    }
};
