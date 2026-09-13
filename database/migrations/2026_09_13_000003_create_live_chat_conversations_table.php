<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained('live_chat_channels')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['open', 'pending', 'closed'])->default('open');
            $table->timestamp('last_message_at')->nullable();
            $table->integer('user_unread_count')->default(0);
            $table->integer('admin_unread_count')->default(0);
            $table->timestamps();

            $table->index(['channel_id', 'user_id']);
            $table->index('status');
            $table->index('last_message_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_chat_conversations');
    }
};
