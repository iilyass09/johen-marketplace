<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveChatMessageAttachment extends Model
{
    protected $fillable = [
        'live_chat_message_id',
        'media_path',
        'media_name',
        'media_mime',
        'media_size',
        'media_duration',
        'poster_path',
        'sort_order',
    ];

    protected $casts = [
        'media_size' => 'integer',
        'media_duration' => 'integer',
        'sort_order' => 'integer',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(LiveChatMessage::class, 'live_chat_message_id');
    }

    public function getMediaUrlAttribute(): ?string
    {
        if (!$this->media_path) {
            return null;
        }
        return media_url($this->media_path);
    }

    public function getPosterUrlAttribute(): ?string
    {
        if (!$this->poster_path) {
            return null;
        }
        return media_url($this->poster_path);
    }
}