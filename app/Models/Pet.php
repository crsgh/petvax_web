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
    
    public function petOwner() {
        return $this->belongsTo(User::class, 'owner_id');
    }
    
    public function species() {
        return $this->belongsTo(Specie::class, 'species_id');
    }
    
    public function breed() {
        return $this->belongsTo(Breed::class, 'breed_id');
    }
}
