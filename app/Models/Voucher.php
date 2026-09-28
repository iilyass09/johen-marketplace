<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Voucher extends Model
{
    public const TYPE_PERCENT = 'percent';
    public const TYPE_FIXED = 'fixed';

    public const SOURCE_GACHA = 'gacha';
    public const SOURCE_MANUAL = 'manual';

    protected $fillable = [
        'code',
        'user_id',
        'gacha_prize_id',
        'source',
        'label',
        'discount_type',
        'discount_value',
        'min_spend',
        'quota',
        'expires_at',
        'order_id',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_spend' => 'integer',
        'quota' => 'integer',
        'used_count' => 'integer',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    protected $appends = ['value_label', 'is_expired', 'is_exhausted'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prize(): BelongsTo
    {
        return $this->belongsTo(GachaPrize::class, 'gacha_prize_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeRedeemable(Builder $query, ?string $code = null): Builder
    {
        if ($code !== null) {
            $query->whereRaw('UPPER(code) = ?', [mb_strtoupper(trim($code))]);
        }

        return $query->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->where(fn ($q) => $q->whereNull('quota')->orWhereColumn('used_count', '<', 'quota'));
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function getIsExhaustedAttribute(): bool
    {
        return $this->quota !== null && (int) $this->used_count >= (int) $this->quota;
    }

    public function isRedeemable(): bool
    {
        return ! $this->is_expired && ! $this->is_exhausted;
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

    public function getTitleAttribute(): string
    {
        return $this->label ?: 'Diskon '.$this->value_label;
    }

    /**
     * Potongan harga setelah voucher. Persentase dibatasi agar subtotal tidak
     * menjadi negatif untuk voucher nominal besar.
     */
    public function discountFor(int $subtotal): int
    {
        if ($subtotal < 1) {
            return 0;
        }

        if ($this->min_spend > 0 && $subtotal < $this->min_spend) {
            return 0;
        }

        $discount = $this->discount_type === self::TYPE_PERCENT
            ? (int) round($subtotal * ((float) $this->discount_value) / 100)
            : (int) round((float) $this->discount_value);

        return max(0, min($discount, $subtotal - 1));
    }

    /**
     * Catat pemakaian voucher. Dipanggil di dalam transaksi pembuatan order
     * setelah baris voucher sudah dikunci.
     */
    public function markUsed(?Order $order = null): void
    {
        $this->increment('used_count');

        $this->forceFill([
            'order_id' => $order?->id,
            'used_at' => now(),
        ])->saveQuietly();
    }

    /**
     * Kembalikan jatah voucher ketika order dibatalkan atau gagal, lalu lepas
     * penanda pemakaian supaya voucher bisa dipakai ulang.
     */
    public function releaseUsage(): void
    {
        if ((int) $this->used_count < 1) {
            $this->forceFill(['order_id' => null, 'used_at' => null])->saveQuietly();

            return;
        }

        $this->decrement('used_count');

        $this->forceFill(['order_id' => null, 'used_at' => null])->saveQuietly();
    }

    public static function generateCode(): string
    {
        do {
            $code = 'JHN-'.Str::upper(Str::random(4).'-'.Str::random(4));
        } while (static::whereRaw('UPPER(code) = ?', [$code])->exists());

        return $code;
    }
}
