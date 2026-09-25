<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiveChatChannel extends Model
{
    public const CS_SLUG = 'johen-cs';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'image',
        'keywords',
        'is_active',
        'is_reception',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_reception' => 'boolean',
    ];

    public function detectionKeywords(): array
    {
        $list = collect(explode(',', (string) $this->keywords))
            ->map(fn ($kw) => mb_strtolower(trim($kw)))
            ->filter()
            ->values();

        if ($list->isEmpty()) {
            $fallback = preg_replace('/^johen\s+/i', '', (string) $this->name);
            $list = collect(preg_split('/[^a-z0-9]+/i', mb_strtolower($fallback)))
                ->filter(fn ($word) => mb_strlen((string) $word) >= 3)
                ->values();
        }

        return $list->all();
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(LiveChatConversation::class, 'channel_id');
    }

    public function operators(): HasMany
    {
        return $this->hasMany(LiveChatOperator::class, 'channel_id');
    }

    public function admins(): HasMany
    {
        return $this->hasMany(LiveChatAdmin::class, 'channel_id');
    }

    public function activeOperators(): HasMany
    {
        return $this->operators()->where('is_active', true);
    }

    public function getActiveOperator()
    {
        $now = now();
        $dayName = strtolower($now->format('l'));
        $currentTime = $now->format('H:i:s');
        $previousDay = strtolower($now->copy()->subDay()->format('l'));

        return $this->activeOperators()
            ->whereHas('schedules', function ($query) use ($dayName, $previousDay, $currentTime) {
                $query->where('is_active', true)
                    ->where(function ($q) use ($dayName, $previousDay, $currentTime) {
                        // Normal: 07:00-18:00
                        $q->where(function ($q2) use ($dayName, $currentTime) {
                            $q2->where('day_of_week', $dayName)
                                ->where('start_time', '<=', $currentTime)
                                ->where('end_time', '>', $currentTime);
                        })
                        // Overnight same day: 19:00-06:00, now=22:50
                        ->orWhere(function ($q2) use ($dayName, $currentTime) {
                            $q2->where('day_of_week', $dayName)
                                ->whereColumn('start_time', '>', 'end_time')
                                ->where('start_time', '<=', $currentTime);
                        })
                        // Overnight previous day: 19:00-06:00, now=03:00
                        ->orWhere(function ($q2) use ($previousDay, $currentTime) {
                            $q2->where('day_of_week', $previousDay)
                                ->whereColumn('start_time', '>', 'end_time')
                                ->where('end_time', '>', $currentTime);
                        });
                    });
            })
            ->with('user')
            ->first();
    }

    public function isCsChannel(): bool
    {
        return $this->slug === static::CS_SLUG;
    }

    public static function csChannel(): self
    {
        return static::firstOrCreate(
            ['slug' => static::CS_SLUG],
            [
                'name' => 'Johen CS',
                'description' => 'Live chat Admin CS untuk pengunjung (guest)',
                'sort_order' => 99,
                'is_active' => true,
            ]
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
