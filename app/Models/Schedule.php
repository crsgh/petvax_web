<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'clinic_id',
        'service_id'
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
