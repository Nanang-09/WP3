<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_order_stays_pending_when_no_active_queue_exists(): void
    {
        $service = $this->createService();

        $response = $this->post(route('order.store'), $this->orderPayload($service));

        $order = Order::first();

        $response->assertRedirect(route('order.success', $order));
        $this->assertSame(Order::STATUS_PENDING, $order->status);
    }

    public function test_new_order_enters_queue_when_there_is_work_in_progress(): void
    {
        config()->set('orders.queue_estimate_days_per_order', 5);

        $service = $this->createService();

        Order::create([
            ...$this->orderPayload($service),
            'order_number' => 'WLD-ACTIVE-0001',
            'status' => Order::STATUS_IN_PROGRESS,
        ]);

        $response = $this->post(route('order.store'), $this->orderPayload($service, [
            'name' => 'Pemesan Baru',
            'email' => 'baru@example.com',
        ]));

        $order = Order::latest('id')->first();

        $response->assertRedirect(route('order.success', $order));
        $this->assertSame(Order::STATUS_QUEUED, $order->status);
        $this->assertSame(1, $order->queue_position);
        $this->assertSame(1, $order->orders_ahead_count);
        $this->assertSame(5, $order->estimated_wait_days);

        $this->get(route('order.success', $order))
            ->assertOk()
            ->assertSee('Sedang Dalam Antrean')
            ->assertSee('#1')
            ->assertSee('5 hari kerja');
    }

    public function test_queue_position_counts_existing_waiting_orders(): void
    {
        config()->set('orders.queue_estimate_days_per_order', 3);

        $service = $this->createService();

        Order::create([
            ...$this->orderPayload($service, ['email' => 'active@example.com']),
            'order_number' => 'WLD-ACTIVE-0002',
            'status' => Order::STATUS_IN_PROGRESS,
        ]);

        Order::create([
            ...$this->orderPayload($service, ['email' => 'queue1@example.com']),
            'order_number' => 'WLD-QUEUE-0001',
            'status' => Order::STATUS_QUEUED,
        ]);

        $this->post(route('order.store'), $this->orderPayload($service, [
            'name' => 'Queue Kedua',
            'email' => 'queue2@example.com',
        ]));

        $order = Order::latest('id')->first();

        $this->assertSame(Order::STATUS_QUEUED, $order->status);
        $this->assertSame(2, $order->queue_position);
        $this->assertSame(2, $order->orders_ahead_count);
        $this->assertSame(6, $order->estimated_wait_days);
    }

    private function createService(): Service
    {
        return Service::create([
            'name' => 'Renovasi Rumah',
            'slug' => 'renovasi-rumah',
            'description' => 'Deskripsi layanan renovasi rumah.',
            'short_description' => 'Renovasi rumah profesional.',
            'icon' => 'fas fa-tools',
            'price_start' => 2500000,
            'price_unit' => 'per proyek',
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    private function orderPayload(Service $service, array $overrides = []): array
    {
        return array_merge([
            'service_id' => $service->id,
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'address' => 'Jl. Mawar No. 10, Jakarta',
            'description' => 'Renovasi dapur dan ruang keluarga.',
            'budget_range' => 'Rp 50 - 100 juta',
            'notes' => 'Mohon survei di hari kerja.',
        ], $overrides);
    }
}
