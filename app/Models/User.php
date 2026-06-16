<?php

namespace App\Models;

use App\Models\Concerns\CascadesDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, CascadesDeletes;

    protected static array $cascades = [
        ['owner_id', Pet::class],
        ['user_id', OTP::class],
        ['user_id', Notification::class],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /**
     * Get the role that owns the user
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the clinic that owns the user
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
