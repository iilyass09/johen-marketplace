<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveChatOperatorSchedule extends Model
{
    protected $fillable = [
        'operator_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(LiveChatOperator::class, 'operator_id');
    }

    public function scopeForDay($query, $day)
    {
        return $query->where('day_of_week', strtolower($day));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isOnDutyNow(): bool
    {
        $now = now();
        $dayName = strtolower($now->format('l'));
        $currentTime = $now->format('H:i:s');

        if ($this->day_of_week !== $dayName || !$this->is_active) {
            return false;
        }

        if (strcmp($this->start_time, $this->end_time) <= 0) {
            return $this->start_time <= $currentTime && $this->end_time > $currentTime;
        }

        return $this->start_time <= $currentTime || $this->end_time > $currentTime;
    }

    public function getScheduleLabelAttribute(): string
    {
        return substr($this->start_time, 0, 5) . ' - ' . substr($this->end_time, 0, 5);
    }
}
