<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'buyer_sku_code',
        'customer_number',
        'customer_name',
        'product_name',
        'brand',
        'category',
        'price',
        'status',
        'note',
    ];

    public function user()
    {
        $this->belongsTo(User::class);
    }

    public function transaction()
    {
        $this->hasOne(Transaction::class);
    }
}
