<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'full_name',
        'username',
        'email',
        'phone',
        'bio',
        'profile_image',
        'password',
        'theme',
        'theme_preferences',
        'notification_settings',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'theme_preferences' => 'array',
        'notification_settings' => 'array',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getNameAttribute()
    {
        return $this->full_name;
    }

    /* =====================
        RELATIONSHIPS
    ===================== */

    public function wardrobes()
    {
        return $this->hasMany(Wardrobe::class);
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class);
    }

    public function shoppingItems()
    {
        return $this->hasMany(ShoppingItem::class);
    }
}
