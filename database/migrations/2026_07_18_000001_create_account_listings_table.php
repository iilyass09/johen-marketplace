<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_listings', function (Blueprint $table) {
            $table->id();
            $table->string('game');
            $table->string('thumbnail')->nullable();
            $table->string('photo')->nullable();
            $table->string('detail_photo_1')->nullable();
            $table->string('detail_photo_2')->nullable();
            $table->string('detail_photo_3')->nullable();
            $table->string('detail_photo_4')->nullable();
            $table->string('detail_photo_5')->nullable();
            $table->string('product_name');
            $table->text('specifications');
            $table->decimal('price', 12, 2);
            $table->string('whatsapp')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_listings');
    }
};
