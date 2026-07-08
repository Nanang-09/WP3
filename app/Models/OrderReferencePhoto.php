<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class OrderReferencePhoto extends Model
{
    protected $fillable = [
        'order_id',
        'uploaded_by',
        'photo_path',
        'caption',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the public URL of the photo.
     */
    public function getPhotoUrlAttribute(): string
    {
        return Storage::url($this->photo_path);
    }
}
