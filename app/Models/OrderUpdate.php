<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderUpdate extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'title',
        'description',
        'progress_percent',
        'photo_path',
        'update_date',
        'status_after_update',
        'is_visible_to_customer',
    ];

    protected $casts = [
        'update_date' => 'date',
        'is_visible_to_customer' => 'boolean',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo_path) {
            return null;
        }

        return asset($this->photo_path);
    }

    public function getVisibilityLabelAttribute(): string
    {
        return $this->is_visible_to_customer ? 'Terlihat oleh pemesan' : 'Internal tim';
    }
}
