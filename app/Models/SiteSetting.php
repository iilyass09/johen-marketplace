<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, $value, string $type = 'text'): void
    {
        static::updateOrCreate(['key' => $key], [
            'value' => $value,
            'type' => $type,
        ]);
        Cache::forget("setting_{$key}");
    }

    public static function allKeyValue(): array
    {
        return static::pluck('value', 'key')->toArray();
    }
}
