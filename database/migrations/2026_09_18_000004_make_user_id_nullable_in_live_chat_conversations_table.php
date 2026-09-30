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

        if ($this->supportsModifySyntax()) {
            DB::statement('ALTER TABLE live_chat_conversations MODIFY user_id BIGINT UNSIGNED NULL');

            return;
        }

        // SQLite tidak mengenal "ALTER TABLE ... MODIFY"; Laravel membangun ulang
        // tabelnya. Foreign key tetap terjaga karena yang didefinisikan ulang
        // hanya kolomnya, bukan constraint-nya.
        Schema::table('live_chat_conversations', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        if ($this->supportsModifySyntax()) {
            DB::statement('ALTER TABLE live_chat_conversations MODIFY user_id BIGINT UNSIGNED NOT NULL');
        } else {
            Schema::table('live_chat_conversations', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable(false)->change();
            });
        }

        Schema::table('live_chat_conversations', function (Blueprint $table) {
            $table->dropIndex(['guest_id']);
            $table->dropColumn(['guest_id', 'guest_name']);
        });
    }

    private function supportsModifySyntax(): bool
    {
        return in_array(DB::getDriverName(), ['mysql', 'mariadb'], true);
    }
};
