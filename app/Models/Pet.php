<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $guarded = [];

    public function clinic() {
        return $this->belongsTo(Clinic::class,'clinic_id');
    }
    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
