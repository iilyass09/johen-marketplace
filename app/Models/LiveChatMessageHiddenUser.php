<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveChatMessageHiddenUser extends Model
{
    protected $fillable = ['live_chat_message_id', 'user_id'];
}
