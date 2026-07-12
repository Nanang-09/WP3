<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        // Pesanan aktif: semua kecuali selesai & dibatalkan
        $activeOrders = Order::with(['service', 'foreman'])
            ->whereNotIn('status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])
            ->latest()
            ->get();

        $pendingCount   = $activeOrders->where('status', Order::STATUS_PENDING)->count();
        $scheduledCount = $activeOrders->where('status', Order::STATUS_SCHEDULED)->count();
        $confirmedCount = $activeOrders->where('status', Order::STATUS_CONFIRMED)->count();
        $inProgressCount = $activeOrders->where('status', Order::STATUS_IN_PROGRESS)->count();
        $completedCount = Order::where('status', Order::STATUS_COMPLETED)->count();

        return view('admin.dashboard', compact(
            'activeOrders',
            'pendingCount',
            'scheduledCount',
            'confirmedCount',
            'inProgressCount',
            'completedCount'
        ));
    }
}
