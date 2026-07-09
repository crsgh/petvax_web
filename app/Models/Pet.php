<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Pet extends Model
{
    // Mongo stores the key as _id; expose "id" in JSON for the frontend
    protected $appends = ['id'];

    use SoftDeletes;
    protected $guarded = [];

    public function clinic() {
        return $this->belongsTo(Clinic::class,'clinic_id');
    }
    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
