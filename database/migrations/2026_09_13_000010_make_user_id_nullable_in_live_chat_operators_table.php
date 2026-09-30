<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ($this->supportsModifySyntax()) {
            DB::unprepared('ALTER TABLE live_chat_operators MODIFY user_id BIGINT UNSIGNED NULL');

            return;
        }

        // SQLite tidak mengenal "ALTER TABLE ... MODIFY"; Laravel membangun ulang
        // tabelnya. Foreign key tetap terjaga karena yang didefinisikan ulang
        // hanya kolomnya, bukan constraint-nya.
        Schema::table('live_chat_operators', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        if ($this->supportsModifySyntax()) {
            DB::unprepared('ALTER TABLE live_chat_operators MODIFY user_id BIGINT UNSIGNED NOT NULL');

            return;
        }

        Schema::table('live_chat_operators', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }

    private function supportsModifySyntax(): bool
    {
        return in_array(DB::getDriverName(), ['mysql', 'mariadb'], true);
    }
};
