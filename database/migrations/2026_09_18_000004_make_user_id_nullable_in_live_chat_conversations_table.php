<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('live_chat_conversations', function (Blueprint $table) {
            $table->string('guest_id', 64)->nullable()->after('user_id');
            $table->string('guest_name', 100)->nullable()->after('guest_id');
            $table->index('guest_id');
        });

        DB::statement('ALTER TABLE live_chat_conversations MODIFY user_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE live_chat_conversations MODIFY user_id BIGINT UNSIGNED NOT NULL');

        Schema::table('live_chat_conversations', function (Blueprint $table) {
            $table->dropIndex(['guest_id']);
            $table->dropColumn(['guest_id', 'guest_name']);
        });
    }
};