<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderMaterial extends Model
{
    protected $fillable = [
        'order_id',
        'material_name',
        'length',
        'shape',
        'quantity',
        'unit_price',
        'subtotal',
        'example_image_path',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
