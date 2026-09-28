<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gacha_prizes', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('discount_type', 20)->default('percent');
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->unsignedInteger('min_spend')->default(0);
            $table->unsignedInteger('weight')->default(10);
            $table->unsignedInteger('quota')->nullable();
            $table->unsignedBigInteger('won_count')->default(0);
            $table->unsignedInteger('validity_days')->default(30);
            $table->string('color', 20)->default('#7c3aed');
            $table->string('icon', 40)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gacha_prizes');
    }
};
