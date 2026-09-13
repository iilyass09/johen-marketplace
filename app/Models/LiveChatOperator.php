<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiveChatOperator extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'channel_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(LiveChatChannel::class, 'channel_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(LiveChatOperatorSchedule::class, 'operator_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForChannel($query, $channelId)
    {
        return $query->where('channel_id', $channelId);
    }

    public function isOnDuty(): bool
    {
        $now = now();
        $dayName = strtolower($now->format('l'));
        $currentTime = $now->format('H:i:s');
        $previousDay = strtolower($now->copy()->subDay()->format('l'));

        return $this->schedules()
            ->where('is_active', true)
            ->where(function ($query) use ($dayName, $previousDay, $currentTime) {
                $query->where(function ($q2) use ($dayName, $currentTime) {
                    $q2->where('day_of_week', $dayName)
                        ->where('start_time', '<=', $currentTime)
                        ->where('end_time', '>', $currentTime);
                })
                ->orWhere(function ($q2) use ($dayName, $currentTime) {
                    $q2->where('day_of_week', $dayName)
                        ->whereColumn('start_time', '>', 'end_time')
                        ->where('start_time', '<=', $currentTime);
                })
                ->orWhere(function ($q2) use ($previousDay, $currentTime) {
                    $q2->where('day_of_week', $previousDay)
                        ->whereColumn('start_time', '>', 'end_time')
                        ->where('end_time', '>', $currentTime);
                });
            })
            ->exists();
    }

    public function getCurrentSchedule()
    {
        $now = now();
        $dayName = strtolower($now->format('l'));
        $currentTime = $now->format('H:i:s');
        $previousDay = strtolower($now->copy()->subDay()->format('l'));

        return $this->schedules()
            ->where('is_active', true)
            ->where(function ($query) use ($dayName, $previousDay, $currentTime) {
                $query->where(function ($q2) use ($dayName, $currentTime) {
                    $q2->where('day_of_week', $dayName)
                        ->where('start_time', '<=', $currentTime)
                        ->where('end_time', '>', $currentTime);
                })
                ->orWhere(function ($q2) use ($dayName, $currentTime) {
                    $q2->where('day_of_week', $dayName)
                        ->whereColumn('start_time', '>', 'end_time')
                        ->where('start_time', '<=', $currentTime);
                })
                ->orWhere(function ($q2) use ($previousDay, $currentTime) {
                    $q2->where('day_of_week', $previousDay)
                        ->whereColumn('start_time', '>', 'end_time')
                        ->where('end_time', '>', $currentTime);
                });
            })
            ->first();
    }
}
