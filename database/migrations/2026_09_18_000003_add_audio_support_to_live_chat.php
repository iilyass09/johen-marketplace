<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('live_chat_messages', function (Blueprint $table) {
            $table->enum('message_type', ['text', 'image', 'video', 'audio'])->default('text')->change();
            $table->unsignedInteger('media_duration')->nullable()->after('media_size');
        });

        Schema::table('live_chat_message_attachments', function (Blueprint $table) {
            $table->unsignedInteger('media_duration')->nullable()->after('media_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_chat_messages', function (Blueprint $table) {
            $table->enum('message_type', ['text', 'image', 'video'])->default('text')->change();
            $table->dropColumn('media_duration');
        });

        Schema::table('live_chat_message_attachments', function (Blueprint $table) {
            $table->dropColumn('media_duration');
        });
    }
};