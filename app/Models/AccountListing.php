<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountListing extends Model
{
    protected $fillable = [
        'game',
        'thumbnail',
        'photo',
        'detail_photo_1',
        'detail_photo_2',
        'detail_photo_3',
        'detail_photo_4',
        'detail_photo_5',
        'product_name',
        'specifications',
        'owner_name',
        'price',
        'original_price',
        'whatsapp',
        'is_active',
        'promo_type',
        'discount_percent',
        'is_sold',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_sold' => 'boolean',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_percent' => 'integer',
    ];

    protected $appends = [
        'thumbnail_url',
        'photo_url',
        'detail_photo_urls',
    ];

    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }
        return null;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return null;
    }

    public function getDetailPhotoUrlsAttribute(): array
    {
        $urls = [];
        for ($i = 1; $i <= 5; $i++) {
            $col = "detail_photo_{$i}";
            if ($this->$col) {
                $urls[] = asset('storage/' . $this->$col);
            }
        }
        return $urls;
    }
}
