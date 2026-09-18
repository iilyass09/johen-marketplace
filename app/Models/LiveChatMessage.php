<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'poster_path',
        'media_name',
        'media_mime',
        'media_size',
        'media_duration',
        'reply_to_message_id',
        'read_at',
    ];

    protected $casts = [
        'media_size' => 'integer',
        'media_duration' => 'integer',
        'read_at' => 'datetime',
    ];

    protected $appends = ['reaction_summary', 'is_starred'];

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

    public function reactions(): HasMany
    {
        return $this->hasMany(LiveChatMessageReaction::class);
    }

    public function stars(): HasMany
    {
        return $this->hasMany(LiveChatMessageStar::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(LiveChatMessageAttachment::class, 'live_chat_message_id')->orderBy('sort_order');
    }

    public function hiddenUsers(): HasMany
    {
        return $this->hasMany(LiveChatMessageHiddenUser::class);
    }

    public function getReactionSummaryAttribute(): array
    {
        if (! $this->relationLoaded('reactions')) {
            return [];
        }

        $userId = auth()->id();

        return $this->reactions->groupBy('emoji')->map(function ($reactions, $emoji) use ($userId) {
            return ['emoji' => $emoji, 'count' => $reactions->count(), 'reacted_by_me' => $reactions->contains('user_id', $userId)];
        })->values()->all();
    }

    public function getIsStarredAttribute(): bool
    {
        return $this->relationLoaded('stars') && $this->stars->contains('user_id', auth()->id());
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
