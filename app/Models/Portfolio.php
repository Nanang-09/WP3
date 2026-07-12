<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

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

    public function getImageUrlAttribute(): ?string
    {
        if (blank($this->image)) {
            return null;
        }

        $relativePath = ltrim($this->image, '/');

        if (! File::exists(public_path($relativePath))) {
            return null;
        }

        return asset($relativePath);
    }
}
