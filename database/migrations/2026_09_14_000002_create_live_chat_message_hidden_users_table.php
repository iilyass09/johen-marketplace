<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_chat_message_hidden_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_chat_message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['live_chat_message_id', 'user_id'], 'lc_hidden_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_chat_message_hidden_users');
    }
};
