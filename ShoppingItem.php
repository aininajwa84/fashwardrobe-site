<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'original_price',
        'platform',
        'product_link',
        'image_url',
        'status',
        'price_alert',
        'purchased_at',
    ];

    protected $casts = [
        'price_alert' => 'boolean',
        'purchased_at' => 'datetime',
    ];

    // Status constants
    const STATUS_WISHLIST = 'wishlist';
    const STATUS_IN_CART = 'in_cart';
    const STATUS_PURCHASED = 'purchased';
    const STATUS_REMOVED = 'removed';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}