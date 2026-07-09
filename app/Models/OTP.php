<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class OTP extends Model
{
    // Mongo stores the key as _id; expose "id" in JSON for the frontend
    protected $appends = ['id'];

    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_verified' => 'boolean',
    ];
}
