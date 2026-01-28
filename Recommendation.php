<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'theme',
        'source',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /* =====================
        RELATIONSHIP
    ===================== */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* =====================
        ACCESSORS
    ===================== */

    public function getResultCountAttribute()
    {
        return $this->data['result_count'] ?? 0;
    }

    public function getSearchCriteriaAttribute()
    {
        $criteria = [];
        
        if (isset($this->data['color']) && $this->data['color']) {
            $criteria[] = 'Color: ' . ucfirst($this->data['color']);
        }
        
        if (isset($this->data['category']) && $this->data['category']) {
            $criteria[] = 'Category: ' . ucfirst($this->data['category']);
        }
        
        return implode(', ', $criteria);
    }
}