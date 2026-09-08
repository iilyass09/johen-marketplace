<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'gateway_invoice_id',
        'gateway_invoice_url',
        'gateway_type',
        'qr_string',
        'va_number',
        'payment_code',
        'checkout_url',
        'gateway_extra',
        'payment_method',
        'buyer_sku_code',
        'customer_number',
        'zone_id',
        'customer_name',
        'email',
        'product_name',
        'brand',
        'category',
        'price',
        'original_price',
        'flash_deal_id',
        'quantity',
        'status',
        'note',
    ];

    protected $appends = ['effective_zone_id'];

    protected $casts = [
        'gateway_extra' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Zone ID efektif untuk ditampilkan.
     * Fallback order lama: zone tersimpan di customer_name berupa angka murni.
     */
    public function getEffectiveZoneIdAttribute(): ?string
    {
        if (!empty($this->zone_id)) {
            return $this->zone_id;
        }

        $name = trim((string) $this->customer_name);

        if ($name !== '' && ctype_digit($name) && !str_contains($this->customer_number, '.')) {
            return $name;
        }

        return null;
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function flashDeal()
    {
        return $this->belongsTo(FlashDeal::class);
    }

    /**
     * Kembalikan kuota flash deal saat pesanan dibatalkan/gagal.
     * Idempotent: flash_deal_id di-null-kan setelah kuota dikembalikan
     * sehingga webhook/permintaan berulang tidak menggandakan kuota.
     */
    public function releaseFlashQuota(): void
    {
        $dealId = $this->flash_deal_id;
        $qty = (int) ($this->quantity ?? 1);

        if (!$dealId || $qty < 1) {
            return;
        }

        $this->update(['flash_deal_id' => null]);

        if ($deal = FlashDeal::find($dealId)) {
            $deal->restockQty($qty);
        }
    }
}
