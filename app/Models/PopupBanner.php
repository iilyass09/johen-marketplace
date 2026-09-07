<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PopupBanner extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'image_fit',
        'image_position',
        'orientation',
        'link',
        'is_active',
        'sort_order',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected $appends = ['image_url'];

    protected static function booted(): void
    {
        static::saved(fn () => static::flushCache());
        static::deleted(fn () => static::flushCache());
    }

    public static function flushCache(): void
    {
        cache()->forget('popup_banners_active_ids');
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    public static function activeBanners()
    {
        $ids = cache()->remember('popup_banners_active_ids', 3600, function () {
            return static::active()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('id')
                ->all();
        });

        if (empty($ids)) {
            return collect();
        }

        return static::whereIn('id', $ids)
            ->orderByRaw('FIELD(id, ' . implode(',', $ids) . ')')
            ->get();
    }

    public function isScheduled(): bool
    {
        return $this->starts_at !== null && $this->ends_at !== null;
    }
}
