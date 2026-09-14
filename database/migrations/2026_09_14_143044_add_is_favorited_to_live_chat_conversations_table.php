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
        Schema::table('live_chat_conversations', function (Blueprint $table) {
            $table->boolean('is_favorited')->default(false)->after('admin_unread_count');
        });
    }

    public function down(): void
    {
        Schema::table('live_chat_conversations', function (Blueprint $table) {
            $table->dropColumn('is_favorited');
        });
    }
};
