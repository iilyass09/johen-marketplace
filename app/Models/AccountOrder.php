<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountOrder extends Model
{
    protected $fillable = [
        'account_listing_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'payment_method',
        'status',
        'total_price',
        'notes',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    public function listing()
    {
        return $this->belongsTo(AccountListing::class, 'account_listing_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
