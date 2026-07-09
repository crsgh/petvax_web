<?php

namespace App\Models;

use App\Models\Concerns\CascadesDeletes;
use MongoDB\Laravel\Eloquent\Model;

class Specie extends Model
{
    use CascadesDeletes;

    protected static array $cascades = [
        ['species_id', Breed::class],
    ];
}
