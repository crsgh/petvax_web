<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class OTP extends Model
{
    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_verified' => 'boolean',
    ];
}
