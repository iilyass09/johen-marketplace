<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTheme extends Model
{
    protected $table = 'event_themes';

    protected $fillable = [
        'name',
        'slug',
        'is_default',
        'start_date',
        'end_date',
        'is_active',
        'priority',
        'colors',
        'logo_override',
        'banner_image',
        'decorative_images',
        'particle_effect',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'priority' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'colors' => 'array',
        'decorative_images' => 'array',
    ];

    public function isAktifSekarang(): bool
    {
        if ($this->is_active) {
            return true;
        }

        $today = now()->startOfDay();
        if ($this->start_date && $this->end_date) {
            return $today->between($this->start_date->copy()->startOfDay(), $this->end_date->copy()->endOfDay());
        }

        return false;
    }

    public function hasVisibleLogo(): bool
    {
        return ! empty($this->logo_override) && is_string($this->logo_override);
    }

    public function hasVisibleBanner(): bool
    {
        return ! empty($this->banner_image) && is_string($this->banner_image);
    }
}