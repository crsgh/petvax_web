<?php

namespace App\Models;

use App\Models\Concerns\CascadesDeletes;
use MongoDB\Laravel\Eloquent\Model;

class Service extends Model
{
    // Mongo stores the key as _id; expose "id" in JSON for the frontend
    protected $appends = ['id'];

    use CascadesDeletes;

    protected static array $cascades = [
        ['service_id', Booking::class],
        ['service_id', Schedule::class],
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }
}
