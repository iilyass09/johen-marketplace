<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_chat_message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_chat_message_id')->constrained('live_chat_messages')->cascadeOnDelete();
            $table->string('media_path');
            $table->string('media_name')->nullable();
            $table->string('media_mime')->nullable();
            $table->bigInteger('media_size')->nullable();
            $table->string('poster_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['live_chat_message_id', 'sort_order'], 'lcma_msg_sort_idx');
        });

        DB::table('live_chat_messages')
            ->whereNotNull('media_path')
            ->orderBy('id')
            ->chunkById(500, function ($messages) {
                $now = now();
                DB::table('live_chat_message_attachments')->insert(
                    $messages->map(fn ($msg) => [
                        'live_chat_message_id' => $msg->id,
                        'media_path' => $msg->media_path,
                        'media_name' => $msg->media_name,
                        'media_mime' => $msg->media_mime,
                        'media_size' => $msg->media_size,
                        'poster_path' => $msg->poster_path,
                        'sort_order' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all()
                );
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_chat_message_attachments');
    }
};