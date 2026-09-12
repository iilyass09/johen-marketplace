<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_orders', function (Blueprint $table) {
            $table->string('order_ref', 40)->nullable()->unique()->after('id');
            $table->string('gateway_invoice_id', 191)->nullable()->after('payment_method');
            $table->string('gateway_type', 30)->nullable()->after('gateway_invoice_id');
            $table->text('qr_string')->nullable()->after('gateway_type');

            $table->index('gateway_invoice_id', 'account_orders_gateway_invoice_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('account_orders', function (Blueprint $table) {
            $table->dropUnique(['order_ref']);
            $table->dropIndex('account_orders_gateway_invoice_id_index');
            $table->dropColumn(['order_ref', 'gateway_invoice_id', 'gateway_type', 'qr_string']);
        });
    }
};