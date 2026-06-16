<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = [];

    public function clinic() {
        return $this->belongsTo(Clinic::class);
    }
    
    public function staff() {
        return $this->belongsTo(User::class, 'staff_id');
    }
    
    public function client() {
        return $this->belongsTo(User::class, 'client_id');
    }
    
    public function pet() {
        return $this->belongsTo(Pet::class);
    }
    
    public function service() {
        return $this->belongsTo(Service::class);
    }
}
