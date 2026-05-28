<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderTransaction extends Model
{
    protected $guarded = [];

    protected $casts = [
        'reward_points'   => 'decimal:4',
        'payout_usd'      => 'decimal:4',
        'platform_profit' => 'decimal:4',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
