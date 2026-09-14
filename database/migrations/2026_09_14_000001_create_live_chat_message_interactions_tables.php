<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_chat_message_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_chat_message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('emoji', 16);
            $table->timestamps();
            $table->unique(['live_chat_message_id', 'user_id', 'emoji'], 'live_chat_reaction_unique');
        });

        Schema::create('live_chat_message_stars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_chat_message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['live_chat_message_id', 'user_id'], 'live_chat_star_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_chat_message_stars');
        Schema::dropIfExists('live_chat_message_reactions');
    }
};
