<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'buyer_sku_code',
        'brand',
        'category',
        'product_name',
        'photo',
        'price',
        'selling_price',
        'type',
        'is_active',
        'stock',
        'region',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'stock' => 'integer',
    ];

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return null;
    }
}
