<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FlashSaleBanner extends Model
{
    protected $fillable = [
        'title',
        'image',
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
        cache()->forget('flash_sale_banners_active_ids');
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return media_url($this->image);
        }
        return null;
    }

    public function scopeActive(Builder $query): Builder
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
        $ids = cache()->remember('flash_sale_banners_active_ids', 3600, function () {
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
}