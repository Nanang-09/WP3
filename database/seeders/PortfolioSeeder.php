<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $portfolios = [
            [
                'title' => 'Rumah Modern Minimalis BSD City',
                'description' => 'Pembangunan rumah modern minimalis 2 lantai dengan luas bangunan 180m². Desain kontemporer dengan konsep open space dan pencahayaan alami maksimal. Material premium dengan finishing cat eksterior dan interior berkualitas tinggi.',
                'location' => 'BSD City, Tangerang Selatan',
                'client_name' => 'Bpk. Ahmad Wijaya',
                'category' => 'Pembangunan Rumah',
                'completion_date' => '2024-06-15',
                'is_featured' => true,
            ],
            [
                'title' => 'Renovasi Kantor PT. Maju Jaya',
                'description' => 'Renovasi total gedung kantor 3 lantai dengan konsep modern industrial. Meliputi perubahan layout, upgrade sistem MEP, dan penambahan area meeting room. Luas area renovasi 450m².',
                'location' => 'Sudirman, Jakarta Selatan',
                'client_name' => 'PT. Maju Jaya Indonesia',
                'category' => 'Renovasi Bangunan',
                'completion_date' => '2024-03-20',
                'is_featured' => true,
            ],
            [
                'title' => 'Struktur Baja Gudang Logistik',
                'description' => 'Fabrikasi dan pemasangan struktur baja untuk gudang logistik seluas 2000m². Konstruksi baja berat dengan spesifikasi WF beam dan kolom, atap truss system, serta lantai beton bertulang.',
                'location' => 'Cikarang, Bekasi',
                'client_name' => 'PT. Logistik Nusantara',
                'category' => 'Konstruksi Baja',
                'completion_date' => '2024-08-10',
                'is_featured' => true,
            ],
            [
                'title' => 'Interior Restoran The Garden',
                'description' => 'Desain dan pembangunan interior restoran dengan konsep tropical garden. Meliputi area dining 200m², bar area, dapur komersial, dan taman indoor. Material alami dikombinasikan dengan elemen modern.',
                'location' => 'Kemang, Jakarta Selatan',
                'client_name' => 'The Garden Restaurant',
                'category' => 'Desain Interior',
                'completion_date' => '2024-01-25',
                'is_featured' => true,
            ],
        ];

        foreach ($portfolios as $portfolio) {
            Portfolio::create(array_merge($portfolio, [
                'slug' => Str::slug($portfolio['title']),
            ]));
        }
    }
}
