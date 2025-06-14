<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{
    public function clinic() {
        return $this->belongsTo(Clinic::class);
    }
    public function pet() {
        return $this->belongsTo(Pet::class);
    }

    public function veterinarian() {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
