<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_chat_operators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('channel_id')->constrained('live_chat_channels')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['channel_id', 'is_active']);
            $table->unique(['user_id', 'channel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_chat_operators');
    }
};
