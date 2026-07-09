<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Notification extends Model
{
    protected $guarded = [];
    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    protected $fillable = [
        'user_id',
        'clinic_id', 
        'title',
        'message',
        'type',
        'is_read',
        'for_user',
        'pet_id'
    ];

    public static function createNotification(array $data = [])
    {
        $defaults = [
            'title' => 'New Booking',
            'message' => 'You have a new booking for your clinic',
            'type' => 'booking',
            'is_read' => 0,
            'for_user' => 0,
            'pet_id' => null
        ];

        return self::create(array_merge($defaults, $data));
    }
}
