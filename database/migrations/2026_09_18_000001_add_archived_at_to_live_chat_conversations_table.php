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
            $table->timestamp('archived_at')->nullable()->after('is_favorited');
        });
    }

    public function down(): void
    {
        Schema::table('live_chat_conversations', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });
    }
};