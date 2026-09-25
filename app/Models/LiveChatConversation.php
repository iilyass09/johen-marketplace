<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LiveChatConversation extends Model
{
    public const SESSION_TIMEOUT_MINUTES = 5;

    public const STATUS_ENDED = 'ended';

    protected $fillable = [
        'channel_id',
        'pending_channel_id',
        'user_id',
        'guest_id',
        'guest_name',
        'guest_email',
        'status',
        'last_message_at',
        'pending_email_attempts',
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
        'pending_email_attempts' => 'integer',
        'is_favorited' => 'boolean',
        'archived_at' => 'datetime',
        'is_pinned' => 'boolean',
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(LiveChatChannel::class, 'channel_id');
    }

    public function pendingChannel(): BelongsTo
    {
        return $this->belongsTo(LiveChatChannel::class, 'pending_channel_id');
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

    public function isGuest(): bool
    {
        return $this->user_id === null && $this->guest_id !== null;
    }

    public function sessionAnchor(): ?\Illuminate\Support\Carbon
    {
        return $this->last_message_at ?: $this->created_at;
    }

    public function isSessionExpired(): bool
    {
        if (! $this->isGuest()) {
            return false;
        }

        $anchor = $this->sessionAnchor();

        return $anchor !== null
            && $anchor->diffInSeconds(now()) >= self::SESSION_TIMEOUT_MINUTES * 60;
    }

    public function applySessionTimeout(): bool
    {
        if ($this->status !== 'open' || ! $this->isSessionExpired()) {
            return false;
        }

        $this->update(['status' => self::STATUS_ENDED]);

        return true;
    }

    public static function expireStaleGuestSessions(?int $channelId = null): void
    {
        static::query()
            ->when($channelId, fn ($q) => $q->where('channel_id', $channelId))
            ->whereNull('user_id')
            ->whereNotNull('guest_id')
            ->where('status', 'open')
            ->get(['id', 'status', 'user_id', 'guest_id', 'last_message_at', 'created_at'])
            ->each(fn (self $conversation) => $conversation->applySessionTimeout());
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
