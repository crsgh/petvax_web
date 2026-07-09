<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class InventoryItem extends Model
{
    public function clinic() {
        return $this->belongsTo(Clinic::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }
}
