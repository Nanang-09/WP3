<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdminPortfolioManagementTest extends TestCase
{
    use RefreshDatabase;

    protected array $createdImages = [];

    public function test_admin_can_create_portfolio_with_image(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.portfolios.store'), [
            'title' => 'Gudang Baja Sentra Niaga',
            'description' => 'Pekerjaan fabrikasi dan instalasi baja untuk gudang logistik.',
            'location' => 'Bekasi',
            'client_name' => 'PT Sentra Niaga',
            'category' => 'Konstruksi Baja',
            'completion_date' => '2026-04-01',
            'is_featured' => '1',
            'image' => UploadedFile::fake()->image('portfolio.jpg'),
        ]);

        $portfolio = Portfolio::first();

        $response->assertRedirect(route('admin.portfolios.index'));
        $this->assertNotNull($portfolio);
        $this->assertSame('gudang-baja-sentra-niaga', $portfolio->slug);
        $this->assertNotNull($portfolio->image);
        $this->assertFileExists(public_path($portfolio->image));

        $this->trackImage($portfolio->image);
    }

    public function test_admin_can_update_portfolio_and_replace_image(): void
    {
        $admin = $this->createAdmin();
        $oldImage = $this->makeExistingImage('existing-portfolio.jpg');

        $portfolio = Portfolio::create([
            'title' => 'Renovasi Kantor Lama',
            'slug' => 'renovasi-kantor-lama',
            'description' => 'Renovasi tahap awal.',
            'location' => 'Jakarta',
            'client_name' => 'PT Lama',
            'image' => $oldImage,
            'category' => 'Renovasi',
            'completion_date' => '2026-03-11',
            'is_featured' => false,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.portfolios.update', $portfolio), [
            'title' => 'Renovasi Kantor Baru',
            'description' => 'Renovasi total kantor dengan tampilan modern.',
            'location' => 'Jakarta Selatan',
            'client_name' => 'PT Baru',
            'category' => 'Renovasi Bangunan',
            'completion_date' => '2026-04-20',
            'image' => UploadedFile::fake()->image('portfolio-baru.png'),
            'is_featured' => '1',
        ]);

        $portfolio->refresh();

        $response->assertRedirect(route('admin.portfolios.index'));
        $this->assertSame('renovasi-kantor-baru', $portfolio->slug);
        $this->assertSame('Jakarta Selatan', $portfolio->location);
        $this->assertTrue($portfolio->is_featured);
        $this->assertNotSame($oldImage, $portfolio->image);
        $this->assertFileDoesNotExist(public_path($oldImage));
        $this->assertFileExists(public_path($portfolio->image));

        $this->trackImage($portfolio->image);
    }

    protected function createAdmin(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
    }

    protected function makeExistingImage(string $filename): string
    {
        $directory = public_path('uploads/portfolios');

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $absolutePath = $directory . DIRECTORY_SEPARATOR . $filename;
        File::put($absolutePath, 'existing-image');

        return 'uploads/portfolios/' . $filename;
    }

    protected function trackImage(?string $relativePath): void
    {
        if (! empty($relativePath)) {
            $this->createdImages[] = $relativePath;
        }
    }

    protected function tearDown(): void
    {
        foreach (array_unique($this->createdImages) as $relativePath) {
            $absolutePath = public_path($relativePath);

            if (File::exists($absolutePath)) {
                File::delete($absolutePath);
            }
        }

        parent::tearDown();
    }
}
