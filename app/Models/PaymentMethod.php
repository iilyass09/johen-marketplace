<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'code',
        'category',
        'icon',
        'photo',
        'photo_light',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['photo_url', 'photo_light_url'];

    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return null;
    }

    public function getPhotoLightUrlAttribute(): ?string
    {
        if ($this->photo_light) {
            return asset('storage/' . $this->photo_light);
        }
        return null;
    }
}
