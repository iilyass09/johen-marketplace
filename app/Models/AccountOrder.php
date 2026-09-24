<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountOrder extends Model
{
    protected $fillable = [
        'account_listing_id',
        'user_id',
        'order_ref',
        'customer_name',
        'customer_email',
        'customer_phone',
        'payment_method',
        'gateway_invoice_id',
        'gateway_invoice_url',
        'gateway_type',
        'qr_string',
        'va_number',
        'payment_code',
        'checkout_url',
        'gateway_extra',
        'status',
        'total_price',
        'notes',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'gateway_extra' => 'array',
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
