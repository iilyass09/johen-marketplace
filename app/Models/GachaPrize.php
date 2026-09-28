<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GachaPrize extends Model
{
    public const TYPE_PERCENT = 'percent';
    public const TYPE_FIXED = 'fixed';

    protected $fillable = [
        'label',
        'discount_type',
        'discount_value',
        'min_spend',
        'weight',
        'quota',
        'validity_days',
        'color',
        'icon',
        'is_active',
        'sort_order',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_spend' => 'integer',
        'weight' => 'integer',
        'quota' => 'integer',
        'won_count' => 'integer',
        'validity_days' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected $appends = ['value_label', 'remaining_quota'];

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }

    public function spins(): HasMany
    {
        return $this->hasMany(GachaSpin::class);
    }

    public function scopeActive(Builder $query, $now = null): Builder
    {
        $now = $now ?? now();

        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
            ->where(fn ($q) => $q->whereNull('quota')->orWhere('quota', '>', 0))
            ->where('weight', '>', 0);
    }

    /**
     * Hadiah yang masih bisa dimasuki undian: aktif, berbobot, dan kuota tersisa.
     * Dipakai di server sebagai sumber kebenaran, bukan hanya tampilan roda.
     */
    public function isEligible($now = null): bool
    {
        $now = $now ?? now();

        if (! $this->is_active || (int) $this->weight < 1) {
            return false;
        }

        if ($this->starts_at && $this->starts_at > $now) {
            return false;
        }

        if ($this->ends_at && $this->ends_at < $now) {
            return false;
        }

        return $this->quota === null || (int) $this->quota > 0;
    }

    public function hasQuota(): bool
    {
        return $this->quota === null || (int) $this->quota > 0;
    }

    public function getValueLabelAttribute(): string
    {
        $value = (float) $this->discount_value;
        $formatted = $this->discount_type === self::TYPE_PERCENT
            ? rtrim(rtrim(number_format($value, 2, ',', '.'), '0'), ',')
            : number_format($value, 0, ',', '.');

        return $this->discount_type === self::TYPE_PERCENT
            ? $formatted.'%'
            : 'Rp'.$formatted;
    }

    public function getRemainingQuotaAttribute(): ?int
    {
        return $this->quota === null ? null : max(0, (int) $this->quota);
    }

    public function consumeQuota(): void
    {
        if ($this->quota !== null) {
            $this->decrement('quota');
        }

        $this->increment('won_count');
    }
}
