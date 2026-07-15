<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'thumbnail',
        'carousel_bg',
        'detail_bg',
        'detail_bg_position',
        'category',
        'service_type',
        'description',
        'is_active',
        'is_popular',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = ['thumbnail_url', 'carousel_bg_url', 'detail_bg_url'];

    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail) {
            return Storage::url($this->thumbnail);
        }
        return null;
    }

    public function getCarouselBgUrlAttribute(): ?string
    {
        if ($this->carousel_bg) {
            return Storage::url($this->carousel_bg);
        }
        return null;
    }

    public function getDetailBgUrlAttribute(): ?string
    {
        if ($this->detail_bg) {
            return Storage::url($this->detail_bg);
        }
        return null;
    }
}
