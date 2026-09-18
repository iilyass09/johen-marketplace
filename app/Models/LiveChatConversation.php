<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LiveChatConversation extends Model
{
    protected $fillable = [
        'channel_id',
        'user_id',
        'status',
        'last_message_at',
        'user_unread_count',
        'admin_unread_count',
        'is_favorited',
        'archived_at',
        'is_pinned',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'user_unread_count' => 'integer',
        'admin_unread_count' => 'integer',
        'is_favorited' => 'boolean',
        'archived_at' => 'datetime',
        'is_pinned' => 'boolean',
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(LiveChatChannel::class, 'channel_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(LiveChatMessage::class, 'conversation_id');
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(LiveChatMessage::class, 'conversation_id')->latestOfMany();
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForChannel($query, $channelId)
    {
        return $query->where('channel_id', $channelId);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['open', 'pending']);
    }

    public function scopeOrderByLastMessage($query)
    {
        return $query->orderBy('last_message_at', 'desc');
    }

    public function markUserRead(): void
    {
        $this->update(['user_unread_count' => 0]);
    }

    public function markAdminRead(): void
    {
        $this->update(['admin_unread_count' => 0]);
    }

    public function incrementUserUnread(): void
    {
        $this->increment('user_unread_count');
    }

    public function incrementAdminUnread(): void
    {
        $this->increment('admin_unread_count');
    }
}
