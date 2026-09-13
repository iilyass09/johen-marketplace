<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('live_chat_conversations')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->enum('sender_type', ['user', 'admin', 'system'])->default('user');
            $table->enum('message_type', ['text', 'image', 'video'])->default('text');
            $table->text('message')->nullable();
            $table->string('media_path')->nullable();
            $table->string('media_name')->nullable();
            $table->string('media_mime')->nullable();
            $table->bigInteger('media_size')->nullable();
            $table->foreignId('reply_to_message_id')->nullable()->constrained('live_chat_messages')->nullOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_chat_messages');
    }
};
