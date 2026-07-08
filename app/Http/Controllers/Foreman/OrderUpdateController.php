<?php

namespace App\Http\Controllers\Foreman;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderUpdate;
use App\Services\CustomerNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class OrderUpdateController extends Controller
{
    public function store(Request $request, Order $order)
    {
        abort_unless((int) $order->foreman_id === (int) auth()->id(), 403, 'Order ini belum ditugaskan ke Anda.');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'progress_percent' => 'required|integer|in:25,50,75,100',
            'update_date' => 'required|date',
            'status_after_update' => 'nullable|in:confirmed,in_progress,completed',
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'is_visible_to_customer' => 'nullable|boolean',
        ]);

        $exists = OrderUpdate::where('order_id', $order->id)
            ->where('progress_percent', $validated['progress_percent'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['progress_percent' => 'Progres ' . $validated['progress_percent'] . '% sudah pernah dikirim sebelumnya.'])->withInput();
        }

        $photoPath = $this->storePhoto($request);

        $statusAfterUpdate = $validated['status_after_update'] ?? null;
        if ($validated['progress_percent'] == 100) {
            $statusAfterUpdate = Order::STATUS_COMPLETED;
        } elseif ($order->status !== Order::STATUS_IN_PROGRESS) {
            $statusAfterUpdate = Order::STATUS_IN_PROGRESS;
        }

        $update = OrderUpdate::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'progress_percent' => $validated['progress_percent'],
            'photo_path' => $photoPath,
            'update_date' => $validated['update_date'],
            'status_after_update' => $statusAfterUpdate,
            'is_visible_to_customer' => $request->boolean('is_visible_to_customer', true),
        ]);

        if ($statusAfterUpdate) {
            $order->update(['status' => $statusAfterUpdate]);
        }

        if ($update->is_visible_to_customer) {
            app(CustomerNotificationService::class)->notifyOrderUpdate($order->fresh(['service', 'user']), $update);
        }

        return redirect()
            ->route('foreman.orders.show', $order)
            ->with('success', 'Update lapangan berhasil dikirim dan langsung tampil ke pemesan.');
    }

    protected function storePhoto(Request $request): string
    {
        $directory = public_path('uploads/order-updates');

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $file = $request->file('photo');
        $filename = now()->format('YmdHis') . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return 'uploads/order-updates/' . $filename;
    }
}
