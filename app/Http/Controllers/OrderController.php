<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Mail\NewOrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

class OrderController extends Controller
{
    public function index()
    {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if (auth()->user()->isForeman()) {
            return redirect()->route('foreman.dashboard');
        }

        $activeStatuses = [
            Order::STATUS_PENDING,
            Order::STATUS_QUEUED,
            Order::STATUS_SCHEDULED,
            Order::STATUS_CONFIRMED,
            Order::STATUS_IN_PROGRESS,
        ];
        $historyStatuses = [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED];

        $allOrders = $this->customerOrdersQuery()->get();

        $activeOrders = $allOrders
            ->filter(fn (Order $o) => in_array($o->status, $activeStatuses))
            ->values()
            ->map(fn (Order $order) => $this->transformOrderForRealtime($order));

        $historyOrders = $allOrders
            ->filter(fn (Order $o) => in_array($o->status, $historyStatuses))
            ->sortByDesc('updated_at')
            ->take(10)
            ->values()
            ->map(fn (Order $order) => $this->transformOrderForRealtime($order));

        $totalHistoryCount = $allOrders
            ->filter(fn (Order $o) => in_array($o->status, $historyStatuses))
            ->count();

        return view('orders.index', [
            'activeOrders'      => $activeOrders,
            'historyOrders'     => $historyOrders,
            'totalHistoryCount' => $totalHistoryCount,
            'pollIntervalMs'    => 15000,
            'lastUpdatedAt'     => now()->translatedFormat('d F Y, H:i:s'),
        ]);
    }

    public function data()
    {
        if (auth()->user()->isAdmin() || auth()->user()->isForeman()) {
            return response()->json(['active_orders' => [], 'history_orders' => [], 'total_history_count' => 0, 'last_updated_at' => now()->translatedFormat('d F Y, H:i:s')]);
        }

        $activeStatuses = [
            Order::STATUS_PENDING,
            Order::STATUS_QUEUED,
            Order::STATUS_SCHEDULED,
            Order::STATUS_CONFIRMED,
            Order::STATUS_IN_PROGRESS,
        ];
        $historyStatuses = [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED];

        $allOrders = $this->customerOrdersQuery()->get();

        $activeOrders = $allOrders
            ->filter(fn (Order $o) => in_array($o->status, $activeStatuses))
            ->values()
            ->map(fn (Order $order) => $this->transformOrderForRealtime($order));

        $historyOrders = $allOrders
            ->filter(fn (Order $o) => in_array($o->status, $historyStatuses))
            ->sortByDesc('updated_at')
            ->take(10)
            ->values()
            ->map(fn (Order $order) => $this->transformOrderForRealtime($order));

        $totalHistoryCount = $allOrders
            ->filter(fn (Order $o) => in_array($o->status, $historyStatuses))
            ->count();

        return response()->json([
            'active_orders'      => $activeOrders,
            'history_orders'     => $historyOrders,
            'total_history_count' => $totalHistoryCount,
            'last_updated_at'   => now()->translatedFormat('d F Y, H:i:s'),
        ]);
    }

    public function create(Service $service)
    {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('error', 'Admin tidak dapat membuat pesanan. Kelola pesanan pelanggan di panel admin.');
        }
        if (auth()->user()->isForeman()) {
            return redirect()->route('foreman.dashboard')->with('error', 'Mandor tidak dapat membuat pesanan.');
        }

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
        abort_unless(auth()->user()->isCustomer(), 403, 'Hanya pelanggan yang dapat membuat pesanan.');

        // ── Proteksi Double-Submit (Backend) ──────────────────────────────
        // Buat fingerprint unik dari kombinasi user + service + alamat
        // Jika request sama datang dalam 30 detik, tolak sebagai duplikat.
        $submissionKey = 'order_submit_' . auth()->id() . '_' . md5(
            $request->input('service_id') . $request->input('address')
        );

        if (cache()->has($submissionKey)) {
            // Kembalikan ke halaman sebelumnya jika duplikat terdeteksi
            return redirect()->back()
                ->withInput()
                ->with('error', 'Pesanan sedang diproses, mohon tunggu sebentar sebelum mencoba lagi.');
        }

        // Kunci selama 30 detik untuk mencegah duplikat
        cache()->put($submissionKey, true, now()->addSeconds(30));
        // ──────────────────────────────────────────────────────────────────

        $validated = $request->validate([
            'service_id'                  => 'required|exists:services,id',
            'name'                        => 'required|string|max:255',
            'email'                       => 'required|email|max:255',
            'phone'                       => 'required|string|max:20',
            'address'                     => 'required|string',
            'budget_range'                => 'nullable|string|max:100',
            'preferred_consultation_date' => 'nullable|date|after:today',
            'preferred_consultation_time' => 'nullable|string|max:100',
        ]);

        $validated['order_number'] = Order::generateOrderNumber();
        $validated['user_id']      = auth()->id();
        $validated['email']        = strtolower($validated['email']);
        $validated['status']       = Order::determineInitialStatus();
        $validated['description']  = 'Detail proyek akan dicatat setelah konsultasi.';

        $order = Order::create($validated);

        // Kirim notifikasi ke admin (Email & WA)
        app(NotificationService::class)->notifyNewOrder($order->load('service'));

        $message = $order->status === Order::STATUS_QUEUED
            ? 'Pesanan berhasil dibuat dan masuk ke antrean pengerjaan.'
            : 'Pesanan berhasil dibuat!';

        return redirect()->route('order.success', $order)->with('success', $message);
    }

    public function edit(Order $order)
    {
        $this->authorizeCustomerOrderMutation($order);

        $service = $order->service;

        $ordersAhead = Order::whereIn('status', [
            Order::STATUS_IN_PROGRESS,
            Order::STATUS_QUEUED,
        ])->count();

        $queuePreview = $ordersAhead > 0 ? [
            'position' => Order::where('status', Order::STATUS_QUEUED)->count() + 1,
            'orders_ahead' => $ordersAhead,
            'estimated_wait_days' => $ordersAhead * Order::queueEstimateDaysPerOrder(),
        ] : null;

        return view('orders.edit', compact('order', 'service', 'queuePreview'));
    }

    public function update(Request $request, Order $order)
    {
        $this->authorizeCustomerOrderMutation($order);

        $validated = $request->validate([
            'name'                        => 'required|string|max:255',
            'email'                       => 'required|email|max:255',
            'phone'                       => 'required|string|max:20',
            'address'                     => 'required|string',
            'budget_range'                => 'nullable|string|max:100',
            'preferred_consultation_date' => 'nullable|date|after:today',
            'preferred_consultation_time' => 'nullable|string',
        ]);

        $order->update($validated);

        return redirect()->route('order.index')->with('success', 'Pesanan Anda berhasil diperbarui!');
    }

    public function cancel(Order $order)
    {
        abort_unless($this->ownsOrder($order), 403, 'Anda tidak memiliki akses ke pesanan ini.');
        abort_unless(in_array($order->status, [
            Order::STATUS_PENDING,
            Order::STATUS_QUEUED,
            Order::STATUS_SCHEDULED,
        ]), 403, 'Pesanan ini tidak dapat dibatalkan pada status saat ini.');

        $order->update([
            'status' => Order::STATUS_CANCELLED,
            'admin_notes' => $order->admin_notes ? $order->admin_notes . "\n(Dibatalkan oleh pelanggan)" : 'Dibatalkan oleh pelanggan',
        ]);

        return redirect()->route('order.index')->with('success', 'Pesanan Anda berhasil dibatalkan.');
    }

    public function acceptAlternativeSchedule(Order $order)
    {
        abort_unless($this->ownsOrder($order), 403, 'Anda tidak memiliki akses ke pesanan ini.');
        abort_unless(
            $order->status === Order::STATUS_SCHEDULED && !$order->is_consultation_confirmed,
            400,
            'Pesanan ini tidak memiliki jadwal alternatif untuk disetujui.'
        );

        $order->update([
            'is_consultation_confirmed' => true,
            'status' => Order::STATUS_SCHEDULED,
        ]);

        return redirect()->route('order.consultation', $order)->with('success', 'Jadwal alternatif disetujui! Proses konsultasi sedang berlangsung.');
    }

    public function success(Order $order)
    {
        $this->authorizeOrderAccess($order);

        // Admin dan Mandor diredirect ke panel masing-masing — halaman ini khusus pelanggan
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.orders.show', $order);
        }
        if (auth()->user()->isForeman()) {
            return redirect()->route('foreman.orders.show', $order);
        }

        // Redirect ke halaman progres jika proyek sudah berjalan atau selesai
        if (in_array($order->status, [Order::STATUS_IN_PROGRESS, Order::STATUS_COMPLETED])) {
            return redirect()->route('order.progress', $order);
        }

        // Redirect ke halaman konsultasi jika konsultasi sedang berlangsung
        if ($order->status === Order::STATUS_SCHEDULED && $order->is_consultation_confirmed) {
            return redirect()->route('order.consultation', $order);
        }

        $order->load(['service', 'foreman', 'updates.user', 'referencePhotos']);

        return view('orders.success', [
            'order' => $order,
            'canManageAsCustomer' => $this->ownsOrder($order),
        ]);
    }

    public function consultation(Order $order)
    {
        $this->authorizeOrderAccess($order);

        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.orders.consultation', $order);
        }
        if (auth()->user()->isForeman()) {
            return redirect()->route('foreman.orders.show', $order);
        }

        // Jika konsultasi sudah selesai dan proyek berjalan, arahkan ke halaman progres
        if (in_array($order->status, [Order::STATUS_IN_PROGRESS, Order::STATUS_COMPLETED])) {
            return redirect()->route('order.progress', $order);
        }

        // If status is not scheduled or not confirmed, redirect to success/details tracking
        if ($order->status !== Order::STATUS_SCHEDULED || !$order->is_consultation_confirmed) {
            return redirect()->route('order.success', $order);
        }

        $order->load(['service']);

        return view('orders.consultation', [
            'order' => $order,
            'canManageAsCustomer' => $this->ownsOrder($order),
        ]);
    }

    public function progress(Order $order)
    {
        $this->authorizeOrderAccess($order);

        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.orders.show', $order);
        }
        if (auth()->user()->isForeman()) {
            return redirect()->route('foreman.orders.show', $order);
        }

        // Hanya untuk proyek yang sedang berjalan atau selesai
        if (!in_array($order->status, [Order::STATUS_IN_PROGRESS, Order::STATUS_COMPLETED])) {
            if ($order->status === Order::STATUS_SCHEDULED && $order->is_consultation_confirmed) {
                return redirect()->route('order.consultation', $order);
            }
            return redirect()->route('order.success', $order);
        }

        $order->load(['service', 'foreman', 'updates.user', 'referencePhotos']);

        return view('orders.progress', [
            'order' => $order,
            'canManageAsCustomer' => $this->ownsOrder($order),
        ]);
    }

    protected function customerOrdersQuery()
    {
        $userId = auth()->id();
        $email = auth()->user()->email;

        return Order::with(['service', 'foreman', 'updates.user'])
            ->where(function ($query) use ($userId, $email) {
                $query->where('user_id', $userId)
                    ->orWhere(function ($subQuery) use ($email) {
                        $subQuery->whereNull('user_id')
                            ->whereRaw('LOWER(email) = ?', [strtolower($email)]);
                    });
            })
            ->latest();
    }

    protected function transformOrderForRealtime(Order $order): array
    {
        return [
            'id'                              => $order->id,
            'order_number'                    => $order->order_number,
            'service_name'                    => $order->service->name,
            'service_slug'                    => $order->service->slug,
            'status'                          => $order->status,
            'status_label'                    => match($order->status) {
                Order::STATUS_PENDING => 'Menunggu admin mengatur jadwal survei',
                Order::STATUS_SCHEDULED => $order->is_consultation_confirmed 
                    ? 'Jadwal survei disepakati, tim akan datang' 
                    : 'Admin mengajukan jadwal alternatif',
                Order::STATUS_QUEUED => 'Dalam antrean pengerjaan',
                Order::STATUS_CONFIRMED => 'Kesepakatan bahan & harga tercapai',
                Order::STATUS_IN_PROGRESS => 'Besi sedang dilas! (Sedang Dikerjakan)',
                Order::STATUS_COMPLETED => 'Proyek selesai 100%',
                Order::STATUS_CANCELLED => 'Pesanan dibatalkan',
                default => $order->status_label,
            },
            'status_color'                    => $order->status_color,
            'budget_range'                    => $order->budget_range,
            'description'                     => $order->description,
            'address'                         => $order->address,
            'notes'                           => $order->notes,
            'admin_notes'                     => $order->admin_notes,
            'foreman_name'                    => $order->foreman?->name,
            'progress_updates_count'          => $order->updates->count(),
            'created_at_label'                => $order->created_at->translatedFormat('d F Y, H:i'),
            'queue_position'                  => $order->queue_position,
            'orders_ahead_count'              => $order->orders_ahead_count,
            'estimated_wait_label'            => $order->estimated_wait_label,
            'detail_url'                      => in_array($order->status, [Order::STATUS_IN_PROGRESS, Order::STATUS_COMPLETED])
                                                    ? route('order.progress', $order)
                                                    : (($order->status === Order::STATUS_SCHEDULED && $order->is_consultation_confirmed)
                                                        ? route('order.consultation', $order)
                                                        : route('order.success', $order)),
            'updates'                         => $this->transformVisibleUpdates($order),
            'can_edit'                        => $order->status === Order::STATUS_PENDING && $this->ownsOrder($order),
            'can_cancel'                      => in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_QUEUED, Order::STATUS_SCHEDULED]) && $this->ownsOrder($order),
            'can_approve_alternative'         => $order->status === Order::STATUS_SCHEDULED && !$order->is_consultation_confirmed && $this->ownsOrder($order),
            'edit_url'                        => route('order.edit', $order),
            'cancel_url'                      => route('order.cancel', $order),
            'accept_alternative_url'          => route('order.accept_alternative', $order),
            'is_consultation_confirmed'       => $order->is_consultation_confirmed,

            // Usulan konsultasi dari pemesan
            'preferred_consultation_date_label' => $order->preferred_consultation_date?->translatedFormat('d F Y'),
            'preferred_consultation_time'       => $order->preferred_consultation_time,

            // Jadwal konsultasi yang sudah dikonfirmasi admin
            'consultation_date_label'         => $order->consultation_date?->translatedFormat('d F Y'),
            'consultation_time'               => $order->consultation_time,
            'consultation_place'              => $order->consultation_place,

            // Detail proyek (setelah kesepakatan)
            'project_start_date_label'        => $order->project_start_date?->translatedFormat('d F Y'),
            'project_end_date_label'          => $order->project_end_date?->translatedFormat('d F Y'),
            'project_price_label'             => null,
        ];
    }


    protected function transformVisibleUpdates(Order $order): array
    {
        return $order->updates
            ->where('is_visible_to_customer', true)
            ->sortByDesc(fn ($update) => $update->update_date->format('Y-m-d') . $update->created_at->format('His'))
            ->values()
            ->map(fn ($update) => [
                'title' => $update->title,
                'description' => $update->description,
                'progress_percent' => $update->progress_percent,
                'photo_url' => $update->photo_url,
                'update_date_label' => $update->update_date->translatedFormat('d F Y'),
                'status_after_update' => $update->status_after_update,
                'status_after_update_label' => $update->status_after_update
                    ? (new Order(['status' => $update->status_after_update]))->status_label
                    : null,
                'updated_by' => $update->user->name,
                'visibility_label' => $update->visibility_label,
            ])
            ->all();
    }

    protected function authorizeOrderAccess(Order $order): void
    {
        if (! auth()->check()) {
            abort(403, 'Silakan login untuk melihat pesanan ini.');
        }

        abort_unless($this->canViewOrder($order), 403, 'Anda tidak memiliki akses ke pesanan ini.');
    }

    protected function authorizeCustomerOrderMutation(Order $order): void
    {
        abort_unless($this->ownsOrder($order), 403, 'Anda tidak memiliki akses ke pesanan ini.');
        abort_unless($order->status === Order::STATUS_PENDING, 403, 'Pesanan ini tidak dapat diubah pada status saat ini.');
    }

    protected function canViewOrder(Order $order): bool
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isForeman() && $order->foreman_id === $user->id) {
            return true;
        }

        return $this->ownsOrder($order);
    }

    protected function ownsOrder(Order $order): bool
    {
        $user = auth()->user();

        if ($order->user_id !== null && (int) $order->user_id === (int) $user->id) {
            return true;
        }

        if ($order->user_id === null && strcasecmp($order->email, $user->email) === 0) {
            $order->update(['user_id' => $user->id]);

            return true;
        }

        return false;
    }
}
