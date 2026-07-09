<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Breed extends Model
{
    // Mongo stores the key as _id; expose "id" in JSON for the frontend
    protected $appends = ['id'];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function species()
    {
        return $this->belongsTo(Specie::class);
    }
}
