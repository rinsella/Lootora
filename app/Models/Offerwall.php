<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Offerwall extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'revenue_share_percentage' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function logoUrl(): ?string
    {
        if (!empty($this->photo_path) && Storage::disk('public')->exists($this->photo_path)) {
            return Storage::url($this->photo_path);
        }
        return null;
    }

    public function initials(): string
    {
        $name = trim((string) $this->name);
        if ($name === '') return '?';
        $parts = preg_split('/\s+/', $name);
        $first = mb_substr($parts[0] ?? '', 0, 1);
        $second = isset($parts[1]) ? mb_substr($parts[1], 0, 1) : mb_substr($parts[0] ?? '', 1, 1);
        return strtoupper($first . $second);
    }
}
