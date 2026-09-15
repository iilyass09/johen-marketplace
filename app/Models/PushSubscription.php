<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'guard',
        'endpoint',
        'public_key',
        'auth_token',
        'user_agent',
    ];
}