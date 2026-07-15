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
}
