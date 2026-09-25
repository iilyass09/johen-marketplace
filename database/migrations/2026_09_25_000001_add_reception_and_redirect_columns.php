<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('live_chat_channels', function (Blueprint $table) {
            $table->text('keywords')->nullable()->after('image');
            $table->boolean('is_reception')->default(false)->after('sort_order');
        });

        Schema::table('live_chat_conversations', function (Blueprint $table) {
            $table->string('guest_email')->nullable()->after('guest_name');
            $table->foreignId('pending_channel_id')->nullable()->after('channel_id')
                ->constrained('live_chat_channels')->nullOnDelete();
            $table->unsignedTinyInteger('pending_email_attempts')->default(0)->after('pending_channel_id');
        });

        DB::table('live_chat_channels')->where('slug', 'johen-cs')->update(['is_reception' => true]);

        $keywords = [
            'johen-pubg' => 'pubg,pbg,battleground,pubg mobile,pubg mobile indo',
            'johen-mlbb' => 'mlbb,ml,mobile legends,mobile legends bang bang,moonton',
            'johen-roblox' => 'roblox,rbx,robux,ro box',
            'johen-e-football' => 'e-football,efootball,efootball mobile,pes mobile,konami',
            'johen-free-fire' => 'free fire,freefire,ff,free fire max',
            'johen-fc-mobile' => 'fc mobile,fcmobile,ea fc,fifa mobile,fifo mobile',
            'johen-valorant' => 'valorant,valo,riot games,valo pontos',
            'monkey-pubg' => 'monkey pubg,monkey',
        ];

        foreach ($keywords as $slug => $value) {
            DB::table('live_chat_channels')->where('slug', $slug)->update(['keywords' => $value]);
        }
    }

    public function down(): void
    {
        Schema::table('live_chat_conversations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pending_channel_id');
            $table->dropColumn('guest_email');
            $table->dropColumn('pending_email_attempts');
        });

        Schema::table('live_chat_channels', function (Blueprint $table) {
            $table->dropColumn('keywords');
            $table->dropColumn('is_reception');
        });
    }
};