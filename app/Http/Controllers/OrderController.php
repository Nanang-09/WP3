<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create(Service $service)
    {
        $ordersAhead = Order::whereIn('status', [
            Order::STATUS_IN_PROGRESS,
            Order::STATUS_QUEUED,
        ])->count();

        $queuePreview = $ordersAhead > 0 ? [
            'position' => Order::where('status', Order::STATUS_QUEUED)->count() + 1,
            'orders_ahead' => $ordersAhead,
            'estimated_wait_days' => $ordersAhead * Order::queueEstimateDaysPerOrder(),
        ] : null;

        return view('orders.create', compact('service', 'queuePreview'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'description' => 'required|string',
            'budget_range' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['order_number'] = Order::generateOrderNumber();
        $validated['user_id'] = auth()->id();
        $validated['status'] = Order::determineInitialStatus();

        $order = Order::create($validated);

        $message = $order->status === Order::STATUS_QUEUED
            ? 'Pesanan berhasil dibuat dan masuk ke antrean pengerjaan.'
            : 'Pesanan berhasil dibuat!';

        return redirect()->route('order.success', $order)->with('success', $message);
    }

    public function success(Order $order)
    {
        $order->load('service');
        return view('orders.success', compact('order'));
    }
}
