<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveChatActivityLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'conversation_id',
        'channel_id',
        'actor_id',
        'actor_type',
        'action',
        'success',
        'message_type',
        'detail',
    ];

    public $timestamps = false;

    protected $casts = [
        'success' => 'boolean',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(LiveChatConversation::class, 'conversation_id');
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(LiveChatChannel::class, 'channel_id');
    }
}
