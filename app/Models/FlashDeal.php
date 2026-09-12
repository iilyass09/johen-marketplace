<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashDeal extends Model
{
    protected $fillable = [
        'product_id',
        'image',
        'discount_percent',
        'stock',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'stock' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $appends = ['flash_price', 'original_price', 'duration_label', 'image_url'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Gambar yang ditampilkan di section FLASH DEAL.
     * Prioritas: gambar khusus flash deal → gambar card game (brand) → foto produk.
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return media_url($this->image);
        }

        $brand = Brand::where('name', $this->product?->brand)->first();

        if ($brand?->featured_thumbnail_url) {
            return $brand->featured_thumbnail_url;
        }

        if ($brand?->thumbnail_url) {
            return $brand->thumbnail_url;
        }

        return $this->product?->photo_url;
    }

    public function scopeActive(Builder $query, $now = null): Builder
    {
        $now = $now ?? now();

        return $query->where('is_active', true)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now)
            ->where('stock', '>', 0)
            ->whereHas('product', fn ($q) => $q->where('is_active', true));
    }

    public function isRunningNow($now = null): bool
    {
        $now = $now ?? now();

        return $this->is_active
            && $this->starts_at && $this->starts_at <= $now
            && $this->ends_at && $this->ends_at >= $now;
    }

    public function getOriginalPriceAttribute(): int
    {
        return (int) ($this->product?->selling_price ?? 0);
    }

    public function getFlashPriceAttribute(): int
    {
        $original = $this->getOriginalPriceAttribute();
        $discount = (float) ($this->discount_percent ?? 0);

        return (int) round($original * (1 - $discount / 100));
    }

    public function getRemainingSecondsAttribute(): int
    {
        if (! $this->ends_at) {
            return 0;
        }

        return max(0, (int) $this->ends_at->getTimestamp() - now()->getTimestamp());
    }

    public function getRemainingLabelAttribute(): string
    {
        return static::formatDuration($this->getRemainingSecondsAttribute());
    }

    public function getDurationLabelAttribute(): string
    {
        if (! $this->starts_at || ! $this->ends_at) {
            return '-';
        }

        $seconds = max(0, (int) $this->ends_at->getTimestamp() - (int) $this->starts_at->getTimestamp());

        return static::formatDuration($seconds);
    }

    public static function formatDuration(int $seconds): string
    {
        if ($seconds <= 0) {
            return 'Selesai';
        }

        if ($seconds < 60) {
            return $seconds.' dtk';
        }

        $minutes = (int) round($seconds / 60);
        if ($minutes < 60) {
            return $minutes.' mnt';
        }

        $hours = (int) floor($minutes / 60);
        $mins = $minutes % 60;

        if ($minutes >= 90 && $hours >= 1) {
            return $mins > 0 ? "{$hours} jam {$mins} mnt" : "{$hours} jam";
        }

        return "{$hours} jam";
    }

    public function consumeQty(int $qty = 1): void
    {
        $this->decrement('stock', max(1, $qty));
    }

    public function restockQty(int $qty = 1): void
    {
        $this->increment('stock', max(1, $qty));
    }
}
