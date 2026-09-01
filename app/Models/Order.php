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
        'buyer_sku_code',
        'customer_number',
        'zone_id',
        'customer_name',
        'email',
        'product_name',
        'brand',
        'category',
        'price',
        'quantity',
        'status',
        'note',
    ];

    protected $appends = ['effective_zone_id'];

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
}
