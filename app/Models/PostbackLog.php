<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostbackLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount'          => 'decimal:4',
        'payout'          => 'decimal:4',
        'signature_valid' => 'boolean',
    ];
}
