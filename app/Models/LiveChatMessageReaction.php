<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveChatMessageReaction extends Model
{
    protected $fillable = ['live_chat_message_id', 'user_id', 'emoji'];
}
