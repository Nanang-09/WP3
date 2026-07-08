<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_QUEUED = 'queued';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id', 'service_id', 'order_number',
        'foreman_id',
        'name', 'email', 'phone', 'address',
        'description', 'budget_range', 'status',
        'notes', 'admin_notes',
        'preferred_consultation_date', 'preferred_consultation_time',
        'consultation_date', 'consultation_time', 'consultation_place',
        'project_start_date', 'project_end_date', 'project_price',
        'agreement_notes',
        'is_consultation_confirmed',
        'preparation_cost', 'delivery_cost', 'total_material_cost',
        'project_requirements',
    ];

    protected $casts = [
        'preferred_consultation_date' => 'date',
        'consultation_date' => 'date',
        'project_start_date' => 'date',
        'project_end_date' => 'date',
        'is_consultation_confirmed' => 'boolean',
        'preparation_cost' => 'integer',
        'delivery_cost' => 'integer',
        'total_material_cost' => 'integer',
        'foreman_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function foreman(): BelongsTo
    {
        return $this->belongsTo(User::class, 'foreman_id');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(OrderUpdate::class)->latest('update_date')->latest();
    }

    public function materials(): HasMany
    {
        return $this->hasMany(OrderMaterial::class);
    }

    public function referencePhotos(): HasMany
    {
        return $this->hasMany(OrderReferencePhoto::class)->latest();
    }



    public function getEstimatedCostAttribute(): int
    {
        return $this->total_material_cost + $this->preparation_cost + $this->delivery_cost;
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'WLD';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -4));
        return "{$prefix}-{$date}-{$random}";
    }

    public static function determineInitialStatus(): string
    {
        return static::STATUS_PENDING;
    }

    public static function queueEstimateDaysPerOrder(): int
    {
        return max(1, (int) config('orders.queue_estimate_days_per_order', 7));
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            static::STATUS_PENDING    => 'Menunggu Tindakan Admin',
            static::STATUS_SCHEDULED  => 'Konsultasi Dijadwalkan',
            static::STATUS_CONFIRMED  => 'Dikonfirmasi',
            static::STATUS_IN_PROGRESS => 'Sedang Dikerjakan',
            static::STATUS_COMPLETED  => 'Selesai',
            static::STATUS_CANCELLED  => 'Dibatalkan',
            static::STATUS_QUEUED     => 'Dalam Antrean',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            static::STATUS_PENDING => '#ffd700',
            static::STATUS_SCHEDULED => '#3b82f6',
            static::STATUS_CONFIRMED => '#00d4ff',
            static::STATUS_IN_PROGRESS => '#7c3aed',
            static::STATUS_COMPLETED => '#10b981',
            static::STATUS_CANCELLED => '#ef4444',
            static::STATUS_QUEUED => '#f59e0b',
            default => '#6b7280',
        };
    }

    public function getQueuePositionAttribute(): ?int
    {
        if ($this->status !== static::STATUS_QUEUED || ! $this->exists) {
            return null;
        }

        return $this->queuedOrdersAheadCount() + 1;
    }

    public function getOrdersAheadCountAttribute(): ?int
    {
        if ($this->status !== static::STATUS_QUEUED || ! $this->exists) {
            return null;
        }

        $inProgressCount = static::where('status', static::STATUS_IN_PROGRESS)->count();

        return $inProgressCount + $this->queuedOrdersAheadCount();
    }

    public function getEstimatedWaitDaysAttribute(): ?int
    {
        if ($this->status !== static::STATUS_QUEUED) {
            return null;
        }

        return ($this->orders_ahead_count ?? 0) * static::queueEstimateDaysPerOrder();
    }

    public function getEstimatedWaitLabelAttribute(): ?string
    {
        if ($this->status !== static::STATUS_QUEUED) {
            return null;
        }

        if (($this->estimated_wait_days ?? 0) <= 0) {
            return 'Giliran Anda berikutnya setelah antrean aktif ditutup admin.';
        }

        return 'Sekitar ' . $this->estimated_wait_days . ' hari kerja';
    }

    protected function queuedOrdersAheadCount(): int
    {
        return static::where('status', static::STATUS_QUEUED)
            ->where(function ($query) {
                $query->where('created_at', '<', $this->created_at)
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('created_at', $this->created_at)
                            ->where('id', '<', $this->id);
                    });
            })
            ->count();
    }

    public function getProgressUpdatesCountAttribute(): int
    {
        return $this->updates()->count();
    }
}
