<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Nominal yang di-"escrow" untuk order ini (isi saldo user).
            $table->decimal('saldo_amount', 15, 2)->default(0)->after('price');
            // none | held | consumed | refunded | reversed
            $table->string('saldo_status', 16)->default('none')->after('saldo_amount');

            // Kapan topUp pertama kali dikirim ke Digiflazz; dasar hitung timeout.
            $table->timestamp('digiflazz_started_at')->nullable()->after('saldo_status');
            $table->unsignedInteger('digiflazz_poll_count')->default(0)->after('digiflazz_started_at');
            $table->timestamp('last_status_check_at')->nullable()->after('digiflazz_poll_count');
            $table->timestamp('saldo_released_at')->nullable()->after('last_status_check_at');

            // none | pending | settled -- untuk kasus topup sukses TELAT
            // setelah saldo sempat dikembalikan.
            $table->string('reconcile_status', 16)->default('none')->after('saldo_released_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'saldo_amount',
                'saldo_status',
                'digiflazz_started_at',
                'digiflazz_poll_count',
                'last_status_check_at',
                'saldo_released_at',
                'reconcile_status',
            ]);
        });
    }
};
