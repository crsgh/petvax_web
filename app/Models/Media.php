<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Media extends Model
{
    // Mongo stores the key as _id; expose "id" in JSON for the frontend
    protected $appends = ['id'];

    protected $guarded = [];

    // Never leak the (large) base64 payload when a parent serializes us.
    protected $hidden = ['data'];
}
