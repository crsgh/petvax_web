<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pet extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function clinic() {
        return $this->belongsTo(Clinic::class,'clinic_id');
    }
    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
