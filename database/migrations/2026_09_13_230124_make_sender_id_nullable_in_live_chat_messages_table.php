<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('ALTER TABLE live_chat_messages MODIFY sender_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::unprepared('ALTER TABLE live_chat_messages MODIFY sender_id BIGINT UNSIGNED NOT NULL');
    }
};
