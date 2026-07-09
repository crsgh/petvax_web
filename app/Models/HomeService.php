<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class HomeService extends Model
{
    // Mongo stores the key as _id; expose "id" in JSON for the frontend
    protected $appends = ['id'];

    //
}
