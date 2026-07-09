<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ActivityRecord extends Model
{
    // Mongo stores the key as _id; expose "id" in JSON for the frontend
    protected $appends = ['id'];

   protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    
}
