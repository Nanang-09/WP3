<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'location',
        'client_name', 'image', 'category',
        'completion_date', 'is_featured',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'completion_date' => 'date',
    ];

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
