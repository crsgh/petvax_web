<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class MedicalHistory extends Model
{
    // Mongo stores the key as _id; expose "id" in JSON for the frontend
    protected $appends = ['id'];

    public function clinic() {
        return $this->belongsTo(Clinic::class);
    }
    public function pet() {
        return $this->belongsTo(Pet::class);
    }

    public function veterinarian() {
        return $this->belongsTo(User::class, 'staff_id');
    }
    public function inventoryItem() {
        return $this->belongsTo(InventoryItem::class, 'inventory_item');
    }
}