<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_QUEUED = 'queued';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id', 'service_id', 'order_number',
        'name', 'email', 'phone', 'address',
        'description', 'budget_range', 'status',
        'notes', 'admin_notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
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
        $hasActiveQueue = static::whereIn('status', [
            static::STATUS_IN_PROGRESS,
            static::STATUS_QUEUED,
        ])->exists();

        return $hasActiveQueue ? static::STATUS_QUEUED : static::STATUS_PENDING;
    }

    public static function queueEstimateDaysPerOrder(): int
    {
        return max(1, (int) config('orders.queue_estimate_days_per_order', 7));
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            static::STATUS_PENDING => 'Menunggu Konfirmasi',
            static::STATUS_QUEUED => 'Sedang Dalam Antrean',
            static::STATUS_CONFIRMED => 'Dikonfirmasi',
            static::STATUS_IN_PROGRESS => 'Sedang Dikerjakan',
            static::STATUS_COMPLETED => 'Selesai',
            static::STATUS_CANCELLED => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            static::STATUS_PENDING => '#ffd700',
            static::STATUS_QUEUED => '#f59e0b',
            static::STATUS_CONFIRMED => '#00d4ff',
            static::STATUS_IN_PROGRESS => '#7c3aed',
            static::STATUS_COMPLETED => '#10b981',
            static::STATUS_CANCELLED => '#ef4444',
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
}
