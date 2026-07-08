<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\NotificationService;

class OrderManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['service', 'user', 'foreman'])
            ->whereNotIn('status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else if ($request->query('tab') === 'new') {
            $query->where('status', Order::STATUS_PENDING);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(10);
        $newOrdersCount = Order::where('status', Order::STATUS_PENDING)->count();
        $activeTab = $request->query('tab', 'all');

        return view('admin.orders.index', compact('orders', 'newOrdersCount', 'activeTab'));
    }

    public function show(Order $order)
    {
        $order->load(['service', 'user', 'foreman', 'updates.user']);
        $foremen = User::where('role', User::ROLE_FOREMAN)->orderBy('name')->get();

        return view('admin.orders.show', compact('order', 'foremen'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,confirmed,in_progress,completed',
            'admin_notes' => 'nullable|string|max:2000',
            'foreman_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', User::ROLE_FOREMAN)),
            ],
            'consultation_date' => 'nullable|date|required_if:status,scheduled',
            'consultation_time' => 'nullable|string|max:100|required_if:status,scheduled',
            'consultation_place' => 'nullable|string|max:255',
            'project_price' => 'nullable|numeric|min:0',
            'project_start_date' => 'nullable|date',
            'project_end_date' => 'nullable|date|after_or_equal:project_start_date',
        ]);

        // Strip consultation fields if not scheduling
        if ($validated['status'] !== Order::STATUS_SCHEDULED) {
            unset($validated['consultation_date'], $validated['consultation_time'], $validated['consultation_place']);
        }

        // Strip project fields if not in confirmed/in_progress/completed stage
        if (! in_array($validated['status'], [Order::STATUS_CONFIRMED, Order::STATUS_IN_PROGRESS, Order::STATUS_COMPLETED])) {
            unset($validated['project_price'], $validated['project_start_date'], $validated['project_end_date']);
        }

        // Auto-confirm consultation when moving to in_progress or beyond
        if (in_array($validated['status'], [Order::STATUS_IN_PROGRESS, Order::STATUS_CONFIRMED, Order::STATUS_COMPLETED])) {
            $validated['is_consultation_confirmed'] = true;
        }

        $order->update($validated);

        if ($order->wasChanged('status') && $order->status === Order::STATUS_SCHEDULED) {
            app(NotificationService::class)->notifyCustomerConsultationScheduled($order);
        }

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    public function consultation(Order $order)
    {
        if ($order->status !== Order::STATUS_SCHEDULED || !$order->is_consultation_confirmed) {
            return redirect()->route('admin.orders.show', $order);
        }

        $order->load(['service', 'user']);

        return view('admin.orders.consultation', compact('order'));
    }

    public function scheduling(Order $order)
    {
        if ($order->status !== Order::STATUS_SCHEDULED || !$order->is_consultation_confirmed) {
            return redirect()->route('admin.orders.show', $order);
        }

        $foremen = User::where('role', User::ROLE_FOREMAN)->orderBy('name')->get();

        return view('admin.orders.scheduling', compact('order', 'foremen'));
    }

    public function saveScheduling(Request $request, Order $order)
    {
        if ($order->status !== Order::STATUS_SCHEDULED || !$order->is_consultation_confirmed) {
            return redirect()->route('admin.orders.show', $order);
        }

        $validated = $request->validate([
            'foreman_id' => [
                'required',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', User::ROLE_FOREMAN)),
            ],
            'project_start_date' => 'required|date',
            'project_end_date' => 'required|date|after_or_equal:project_start_date',
            'agreement_notes' => 'nullable|string|max:2000',
        ], [
            'foreman_id.required' => 'Mandor wajib ditugaskan.',
            'project_start_date.required' => 'Tanggal mulai proyek wajib diisi.',
            'project_end_date.required' => 'Tanggal selesai proyek wajib diisi.',
            'project_end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        $validated['status'] = Order::STATUS_IN_PROGRESS;

        $order->update($validated);

        $order->load(['foreman']);
        $notifService = app(NotificationService::class);
        $notifService->notifyForemanAssigned($order);
        $notifService->notifyCustomerProjectStarted($order);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Konsultasi selesai dan proyek berhasil dijadwalkan! Proyek sekarang dalam tahap pengerjaan.');
    }

    public function reject(Request $request, Order $order)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ]);

        $order->update([
            'status' => Order::STATUS_CANCELLED,
            'admin_notes' => 'Ditolak: ' . $validated['rejection_reason'],
        ]);

        return redirect()->back()->with('success', 'Pesanan berhasil ditolak.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Pesanan berhasil dihapus secara permanen!');
    }

    public function completed(Request $request)
    {
        $query = Order::with(['service', 'foreman'])
            ->where('status', Order::STATUS_COMPLETED)
            ->latest('updated_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $completedOrders = $query->paginate(15);

        return view('admin.orders.completed', compact('completedOrders'));
    }

    public function history(Request $request)
    {
        $historyStatuses = [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED];

        $baseQuery = Order::with(['service', 'foreman', 'user'])
            ->whereIn('status', $historyStatuses)
            ->latest('updated_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Total di database (semua, tidak dibatasi)
        $totalHistoryCount = (clone $baseQuery)->count();

        // Untuk showcase: ambil 10 terbaru saja (hanya jika tidak ada pencarian)
        // Jika ada pencarian, tampilkan semua hasil pencarian
        if ($request->filled('search')) {
            $historyOrders = $baseQuery->paginate(15);
            $showcaseMode = false;
        } else {
            $historyOrders = $baseQuery->paginate(10);
            $showcaseMode = true;
        }

        return view('admin.orders.history', compact(
            'historyOrders',
            'totalHistoryCount',
            'showcaseMode'
        ));
    }

    public function checkNewOrders(Request $request)
    {
        $lastCheckedId = $request->query('last_checked_id');

        $query = Order::where('status', Order::STATUS_PENDING);

        if ($lastCheckedId) {
            $query->where('id', '>', $lastCheckedId);
        }

        $newOrders = $query->with('service')->get();
        $latestOrder = Order::where('status', Order::STATUS_PENDING)->latest('id')->first();

        return response()->json([
            'new_orders' => $newOrders->map(fn($o) => [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'name' => $o->name,
                'service_name' => $o->service->name,
                'created_at' => $o->created_at->format('H:i'),
                'show_url' => route('admin.orders.show', $o->id)
            ]),
            'latest_pending_id' => $latestOrder ? $latestOrder->id : 0,
            'total_pending' => Order::where('status', Order::STATUS_PENDING)->count()
        ]);
    }

    public function updateRequirements(Request $request, Order $order)
    {
        $validated = $request->validate([
            'project_requirements' => 'nullable|string|max:10000',
        ]);

        $order->update($validated);

        return redirect()->back()->with('success', 'Catatan kebutuhan proyek berhasil diperbarui!');
    }
}

