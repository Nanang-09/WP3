<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_order_stays_pending_when_no_active_queue_exists(): void
    {
        $this->actingAs($this->createCustomer());

        $service = $this->createService();

        $response = $this->post(route('order.store'), $this->orderPayload($service));

        $order = Order::first();

        $response->assertRedirect(route('order.success', $order));
        $this->assertSame(Order::STATUS_PENDING, $order->status);
    }

    public function test_new_order_starts_pending_even_with_work_in_progress_and_can_be_scheduled(): void
    {
        $customer = $this->createCustomer();
        $this->actingAs($customer);

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
        $this->assertSame(Order::STATUS_PENDING, $order->status);
        $this->assertSame($customer->id, $order->user_id);

        $this->get(route('order.success', $order))
            ->assertOk()
            ->assertSee('Menunggu Tindakan Admin')
            ->assertSee('Usulan Jadwal Anda');

        // Admin schedules the consultation
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin)
            ->put(route('admin.orders.updateStatus', $order), [
                'status' => Order::STATUS_SCHEDULED,
                'consultation_date' => now()->addDays(3)->toDateString(),
                'consultation_time' => '13:00 - 15:00 (Siang)',
                'consultation_place' => 'Kantor CV WeldTrack',
            ])->assertRedirect();

        $order->refresh();
        $this->assertSame(Order::STATUS_SCHEDULED, $order->status);
        $this->assertSame(now()->addDays(3)->toDateString(), $order->consultation_date->toDateString());
        $this->assertSame('13:00 - 15:00 (Siang)', $order->consultation_time);
        $this->assertSame('Kantor CV WeldTrack', $order->consultation_place);
    }

    public function test_admin_can_accept_schedule_directly_to_consultation(): void
    {
        $customer = $this->createCustomer();
        $this->actingAs($customer);
        $service = $this->createService();

        $this->post(route('order.store'), $this->orderPayload($service));
        $order = Order::first();

        // Create admin
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        // Admin accepts schedule (sets to scheduled)
        $this->actingAs($admin)
            ->put(route('admin.orders.updateStatus', $order), [
                'status' => Order::STATUS_SCHEDULED,
                'consultation_date' => $order->preferred_consultation_date->toDateString(),
                'consultation_time' => $order->preferred_consultation_time,
                'consultation_place' => $order->address,
            ])->assertRedirect();

        $order->refresh();
        $this->assertSame(Order::STATUS_SCHEDULED, $order->status);

        // Admin moves to confirmed after consultation
        $this->actingAs($admin)
            ->put(route('admin.orders.updateStatus', $order), [
                'status' => Order::STATUS_CONFIRMED,
            ])->assertRedirect();

        $order->refresh();
        $this->assertSame(Order::STATUS_CONFIRMED, $order->status);
        $this->assertTrue($order->is_consultation_confirmed);
    }

    public function test_admin_proposes_alternative_schedule_and_customer_approves_to_consultation(): void
    {
        $customer = $this->createCustomer();
        $this->actingAs($customer);
        $service = $this->createService();

        $this->post(route('order.store'), $this->orderPayload($service));
        $order = Order::first();

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        // Admin proposes alternative (scheduled but not confirmed)
        $this->actingAs($admin)
            ->put(route('admin.orders.updateStatus', $order), [
                'status' => Order::STATUS_SCHEDULED,
                'is_consultation_confirmed' => 0,
                'consultation_date' => now()->addDays(3)->toDateString(),
                'consultation_time' => '10:00 WIB',
                'consultation_place' => 'Kantor WeldTrack',
                'agreement_notes' => 'Jadwal alternatif.',
            ])->assertRedirect();

        $order->refresh();
        $this->assertSame(Order::STATUS_SCHEDULED, $order->status);
        $this->assertFalse($order->is_consultation_confirmed);

        // Customer approves the alternative (sets to scheduled and confirmed)
        $this->actingAs($customer)
            ->post(route('order.accept_alternative', $order))
            ->assertRedirect(route('order.consultation', $order));

        $order->refresh();
        $this->assertSame(Order::STATUS_SCHEDULED, $order->status);
        $this->assertTrue($order->is_consultation_confirmed);
    }

    public function test_admin_can_schedule_project_after_consultation(): void
    {
        $customer = $this->createCustomer();
        $service = $this->createService();

        $order = Order::create([
            ...$this->orderPayload($service),
            'order_number' => 'WLD-TEST-1234',
            'user_id' => $customer->id,
            'status' => Order::STATUS_SCHEDULED,
            'is_consultation_confirmed' => true,
            'consultation_date' => now()->toDateString(),
            'consultation_time' => '10:00',
            'consultation_place' => 'Lokasi Proyek',
        ]);

        $foreman = User::factory()->create(['role' => User::ROLE_FOREMAN]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('admin.orders.saveScheduling', $order), [
                'foreman_id' => $foreman->id,
                'project_start_date' => now()->addDays(2)->toDateString(),
                'project_end_date' => now()->addDays(12)->toDateString(),
                'agreement_notes' => 'Catatan kesepakatan final.',
            ])->assertRedirect(route('admin.orders.show', $order));

        $order->refresh();
        $this->assertSame(Order::STATUS_IN_PROGRESS, $order->status);
        $this->assertEquals($foreman->id, $order->foreman_id);
        $this->assertSame(now()->addDays(2)->toDateString(), $order->project_start_date->toDateString());
        $this->assertSame(now()->addDays(12)->toDateString(), $order->project_end_date->toDateString());
        $this->assertSame('Catatan kesepakatan final.', $order->agreement_notes);
        $this->assertNull($order->project_price);

        // Customer should now be redirected to the progress page
        $this->actingAs($customer)
            ->get(route('order.success', $order))
            ->assertRedirect(route('order.progress', $order));

        $this->actingAs($customer)
            ->get(route('order.consultation', $order))
            ->assertRedirect(route('order.progress', $order));

        $this->actingAs($customer)
            ->get(route('order.progress', $order))
            ->assertOk();
    }

    public function test_queue_position_counts_existing_waiting_orders(): void
    {
        $this->actingAs($this->createCustomer());

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

        $order = Order::create([
            ...$this->orderPayload($service, ['email' => 'queue2@example.com']),
            'order_number' => 'WLD-QUEUE-0002',
            'status' => Order::STATUS_QUEUED,
        ]);

        $this->assertSame(Order::STATUS_QUEUED, $order->status);
        $this->assertSame(2, $order->queue_position);
        $this->assertSame(2, $order->orders_ahead_count);
        $this->assertSame(6, $order->estimated_wait_days);
    }

    public function test_guest_must_login_before_accessing_order_pages(): void
    {
        $service = $this->createService();

        $this->get(route('order.create', $service))
            ->assertRedirect(route('login'));

        $this->post(route('order.store'), $this->orderPayload($service))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_manage_project_requirements_and_reference_photos(): void
    {
        $customer = $this->createCustomer();
        $service = $this->createService();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $order = Order::create([
            ...$this->orderPayload($service),
            'order_number' => 'WLD-TEST-1234',
            'user_id' => $customer->id,
            'status' => Order::STATUS_SCHEDULED,
        ]);

        // 1. Admin updates project requirements (text biasa)
        $this->actingAs($admin)
            ->put(route('admin.orders.updateRequirements', $order), [
                'project_requirements' => "Spesifikasi Kanopi:\n- Panjang: 5m\n- Lebar: 3m\n- Bahan: Galvanis",
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'project_requirements' => "Spesifikasi Kanopi:\n- Panjang: 5m\n- Lebar: 3m\n- Bahan: Galvanis",
        ]);

        // 2. Admin uploads a reference photo
        \Illuminate\Support\Facades\Storage::fake('public');
        $fakePhoto = \Illuminate\Http\UploadedFile::fake()->image('desain_kanopi.jpg');

        $this->actingAs($admin)
            ->post(route('admin.orders.photos.store', $order), [
                'photo' => $fakePhoto,
                'caption' => 'Desain Kanopi Minimalis',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('order_reference_photos', [
            'order_id' => $order->id,
            'caption' => 'Desain Kanopi Minimalis',
        ]);

        $photo = \App\Models\OrderReferencePhoto::first();
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($photo->photo_path);

        // 3. Customer views the tracking page and sees the requirements and photos
        $this->actingAs($customer)
            ->get(route('order.success', $order))
            ->assertOk()
            ->assertSee('Spesifikasi Kanopi:')
            ->assertSee('- Panjang: 5m')
            ->assertSee('- Lebar: 3m')
            ->assertSee('- Bahan: Galvanis')
            ->assertSee('Desain Kanopi Minimalis')
            ->assertSee($photo->photo_url);

        // 4. Admin deletes the photo
        $this->actingAs($admin)
            ->delete(route('admin.orders.photos.destroy', $photo))
            ->assertRedirect();

        $this->assertDatabaseMissing('order_reference_photos', [
            'caption' => 'Desain Kanopi Minimalis',
        ]);
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($photo->photo_path);
    }

    public function test_admin_can_update_notes_for_scheduled_consultation(): void
    {
        $customer = $this->createCustomer();
        $service = $this->createService();

        $order = Order::create([
            ...$this->orderPayload($service),
            'order_number' => 'WLD-TEST-9999',
            'user_id' => $customer->id,
            'status' => Order::STATUS_SCHEDULED,
            'is_consultation_confirmed' => true,
            'consultation_date' => now()->toDateString(),
            'consultation_time' => '10:00',
            'consultation_place' => 'Lokasi Proyek',
        ]);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        // Admin updates notes without providing consultation date/time
        $response = $this->actingAs($admin)
            ->put(route('admin.orders.updateStatus', $order), [
                'status' => Order::STATUS_SCHEDULED,
                'is_consultation_confirmed' => 1,
                'admin_notes' => 'Catatan admin baru.',
            ]);

        $response->assertRedirect();
        $order->refresh();
        $this->assertSame('Catatan admin baru.', $order->admin_notes);
        $this->assertTrue($order->is_consultation_confirmed);
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

    private function createCustomer(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
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
            'preferred_consultation_date' => now()->addDays(2)->toDateString(),
            'preferred_consultation_time' => '09:00 - 11:00 (Pagi)',
            'notes' => 'Mohon survei di hari kerja.',
        ], $overrides);
    }
}
