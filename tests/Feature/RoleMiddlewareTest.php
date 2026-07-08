<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $response = $this->actingAs($customer)->get(route('admin.dashboard'));

        $response->assertRedirect(route('order.index'));
        $response->assertSessionHas('error', 'Halaman admin hanya dapat diakses oleh administrator.');
    }

    public function test_customer_cannot_access_foreman_dashboard(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $response = $this->actingAs($customer)->get(route('foreman.dashboard'));

        $response->assertRedirect(route('order.index'));
        $response->assertSessionHas('error', 'Panel mandor hanya untuk akun mandor lapangan.');
    }

    public function test_admin_cannot_access_foreman_dashboard(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('foreman.dashboard'));

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('error', 'Kelola semua pesanan dan mandor melalui panel admin.');
    }

    public function test_foreman_cannot_access_admin_dashboard(): void
    {
        $foreman = User::factory()->create(['role' => User::ROLE_FOREMAN]);

        $response = $this->actingAs($foreman)->get(route('admin.dashboard'));

        $response->assertRedirect(route('foreman.dashboard'));
        $response->assertSessionHas('error', 'Panel admin hanya untuk administrator. Mandor dapat mengelola pesanan yang ditugaskan di panel mandor.');
    }
}
