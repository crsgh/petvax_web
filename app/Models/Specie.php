<?php

namespace App\Models;

use App\Models\Concerns\CascadesDeletes;
use MongoDB\Laravel\Eloquent\Model;

class Specie extends Model
{
    // Mongo stores the key as _id; expose "id" in JSON for the frontend
    protected $appends = ['id'];

    use CascadesDeletes;

    protected static array $cascades = [
        ['species_id', Breed::class],
    ];
}
