<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'thumbnail',
        'featured_thumbnail',
        'featured_img_1',
        'featured_img_2',
        'featured_img_3',
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

    protected $appends = ['thumbnail_url', 'featured_thumbnail_url', 'featured_img_urls', 'carousel_bg_url', 'detail_bg_url'];

    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }
        return null;
    }

    public function getFeaturedThumbnailUrlAttribute(): ?string
    {
        if ($this->featured_thumbnail) {
            return asset('storage/' . $this->featured_thumbnail);
        }
        return null;
    }

    public function getFeaturedImgUrlsAttribute(): array
    {
        $urls = [];
        foreach (['featured_img_1', 'featured_img_2', 'featured_img_3'] as $col) {
            if ($this->$col) {
                $urls[] = asset('storage/' . $this->$col);
            }
        }
        return $urls;
    }

    public function getCarouselBgUrlAttribute(): ?string
    {
        if ($this->carousel_bg) {
            return asset('storage/' . $this->carousel_bg);
        }
        return null;
    }

    public function getDetailBgUrlAttribute(): ?string
    {
        if ($this->detail_bg) {
            return asset('storage/' . $this->detail_bg);
        }
        return null;
    }
}
