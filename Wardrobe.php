<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wardrobe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'color',
        'theme',
        'image',
        'notes',
    ];

    // Relationship dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define category options
    public static function categories()
    {
        return [
            'top' => 'Top (Shirts, Blouses, T-Shirts)',
            'bottom' => 'Bottom (Pants, Skirts, Shorts)',
            'dress' => 'Dress',
            'shoes' => 'Shoes',
            'accessory' => 'Accessory (Bags, Jewelry, Belts)',
            'outerwear' => 'Outerwear (Jackets, Coats)',
            'formal' => 'Formal Wear',
            'casual' => 'Casual Wear',
            'sportswear' => 'Sportswear',
        ];
    }

    // Define color options
    public static function colors()
    {
        return [
            'black' => 'Black',
            'white' => 'White',
            'gray' => 'Gray',
            'navy' => 'Navy',
            'blue' => 'Blue',
            'red' => 'Red',
            'green' => 'Green',
            'yellow' => 'Yellow',
            'purple' => 'Purple',
            'pink' => 'Pink',
            'orange' => 'Orange',
            'brown' => 'Brown',
            'beige' => 'Beige',
            'multi' => 'Multi-color',
        ];
    }

    // Accessor for image URL
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/default-clothing.jpg');
    }
}