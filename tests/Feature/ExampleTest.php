<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        Service::create([
            'name' => 'Pagar Besi',
            'slug' => 'pagar-besi',
            'description' => 'Pembuatan pagar besi untuk rumah.',
            'short_description' => 'Pagar besi minimalis dan kuat.',
            'icon' => 'fas fa-hammer',
            'price_start' => 1500000,
            'price_unit' => 'per proyek',
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Portfolio::create([
            'title' => 'Renovasi Rumah Tinggal',
            'slug' => 'renovasi-rumah-tinggal',
            'description' => 'Renovasi fasad dan pagar rumah tinggal.',
            'location' => 'Jakarta Selatan',
            'client_name' => 'Budi',
            'category' => 'Renovasi',
            'completion_date' => now()->subMonth(),
            'is_featured' => true,
        ]);

        Testimonial::create([
            'name' => 'Andi',
            'role' => 'Pemilik Rumah',
            'content' => 'Pengerjaan rapi dan komunikasinya jelas.',
            'rating' => 5,
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Layanan yang paling sering dicari');
    }
}
