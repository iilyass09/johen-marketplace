<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LiveChatMessage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_type',
        'message_type',
        'message',
        'media_path',
        'media_name',
        'media_mime',
        'media_size',
        'reply_to_message_id',
        'read_at',
    ];

    protected $casts = [
        'media_size' => 'integer',
        'read_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(LiveChatConversation::class, 'conversation_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(LiveChatMessage::class, 'reply_to_message_id');
    }

    public function replies()
    {
        return $this->hasMany(LiveChatMessage::class, 'reply_to_message_id');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markRead(): void
    {
        if (!$this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }

    public function getMediaUrlAttribute(): ?string
    {
        if (!$this->media_path) {
            return null;
        }
        return media_url($this->media_path);
    }

    public function scopeForConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    public function scopeAfter($query, $messageId)
    {
        return $query->where('id', '>', $messageId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'asc');
    }
}
