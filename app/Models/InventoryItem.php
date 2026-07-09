<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class InventoryItem extends Model
{
    // Mongo stores the key as _id; expose "id" in JSON for the frontend
    protected $appends = ['id'];

    public function clinic() {
        return $this->belongsTo(Clinic::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }
}
