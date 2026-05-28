<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $guarded = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        $cacheKey = 'site_setting:'.$key;

        return Cache::rememberForever($cacheKey, function () use ($key, $default) {
            $row = static::where('key', $key)->first();
            if (!$row) {
                return $default;
            }
            return match ($row->type) {
                'int'   => (int) $row->value,
                'float' => (float) $row->value,
                'bool'  => filter_var($row->value, FILTER_VALIDATE_BOOLEAN),
                'json'  => json_decode($row->value, true),
                default => $row->value,
            };
        });
    }

    public static function put(string $key, mixed $value, string $type = 'string', string $group = 'general'): void
    {
        $stored = match ($type) {
            'json' => json_encode($value),
            'bool' => $value ? '1' : '0',
            default => is_scalar($value) ? (string) $value : json_encode($value),
        };

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'type' => $type, 'group' => $group],
        );

        Cache::forget('site_setting:'.$key);
    }
}
