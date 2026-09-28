<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('gacha_prize_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source', 20)->default('gacha');
            $table->string('label')->nullable();
            $table->string('discount_type', 20)->default('percent');
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->unsignedInteger('min_spend')->default(0);
            $table->unsignedInteger('quota')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('used_at')->nullable();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->timestamps();

            $table->index(['user_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
