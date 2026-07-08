<?php

namespace App\Http\Controllers\Foreman;

use App\Http\Controllers\Controller;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $orders = Order::with(['service', 'user', 'updates'])
            ->where('foreman_id', auth()->id())
            ->latest()
            ->get();

        $stats = [
            'assigned_orders' => $orders->count(),
            'active_orders' => $orders->whereIn('status', [
                Order::STATUS_CONFIRMED,
                Order::STATUS_IN_PROGRESS,
                Order::STATUS_QUEUED,
                Order::STATUS_PENDING,
                Order::STATUS_SCHEDULED,
            ])->count(),
            'completed_orders' => $orders->where('status', Order::STATUS_COMPLETED)->count(),
            'updates_today' => $orders->sum(
                fn (Order $order) => $order->updates->filter(fn ($update) => $update->update_date?->isToday())->count()
            ),
        ];

        return view('foreman.dashboard', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        abort_unless((int) $order->foreman_id === (int) auth()->id(), 403, 'Order ini belum ditugaskan ke Anda.');

        $order->load(['service', 'user', 'updates.user']);

        return view('foreman.orders.show', compact('order'));
    }
}
