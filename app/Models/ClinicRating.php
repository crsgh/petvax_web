<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ClinicRating extends Model
{
    protected $guarded = [];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
