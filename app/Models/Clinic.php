<?php

namespace App\Models;

use App\Models\Concerns\CascadesDeletes;
use MongoDB\Laravel\Eloquent\Model;

class Clinic extends Model
{
    use CascadesDeletes;

    protected $guarded = [];

    protected static array $cascades = [
        ['clinic_id', User::class],
        ['clinic_id', MedicalHistory::class],
        ['clinic_id', Service::class],
        ['clinic_id', Booking::class],
        ['clinic_id', InventoryItem::class],
        ['clinic_id', Specie::class],
        ['clinic_id', Breed::class],
        ['clinic_id', ClinicRating::class],
        ['clinic_id', ActivityRecord::class],
        ['clinic_id', Schedule::class],
    ];

    public function ratings()
    {
        return $this->hasMany(ClinicRating::class, 'clinic_id');
    }
}
