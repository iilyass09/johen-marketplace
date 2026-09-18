<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_chat_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('live_chat_conversations')->cascadeOnDelete();
            $table->foreignId('channel_id')->constrained('live_chat_channels')->cascadeOnDelete();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('actor_type', 20)->default('user');
            $table->string('action', 24);
            $table->boolean('success')->default(false);
            $table->string('message_type', 16)->default('text');
            $table->text('detail')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_chat_activity_logs');
    }
};
