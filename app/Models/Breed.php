<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Breed extends Model
{
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function species()
    {
        return $this->belongsTo(Specie::class);
    }
}
