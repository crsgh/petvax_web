<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }
}
