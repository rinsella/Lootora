<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralEarning extends Model
{
    protected $guarded = [];

    protected $casts = [
        'points' => 'decimal:4',
    ];

    public function earner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}
