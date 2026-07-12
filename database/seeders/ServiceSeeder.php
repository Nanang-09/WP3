<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Pembangunan Rumah',
                'short_description' => 'Layanan pembangunan rumah dari nol hingga siap huni dengan desain modern dan material berkualitas tinggi.',
                'description' => 'Kami menyediakan layanan pembangunan rumah dari awal hingga selesai. Tim arsitek dan insinyur berpengalaman kami akan membantu Anda mewujudkan rumah impian dengan desain modern, material premium, dan standar konstruksi terbaik. Mulai dari perencanaan, pondasi, struktur, hingga finishing.',
                'icon' => 'fas fa-home',
                'price_start' => 850000,
                'price_unit' => 'per meter',
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Renovasi Bangunan',
                'short_description' => 'Renovasi dan perbaikan bangunan untuk memperbarui tampilan serta meningkatkan fungsi ruang.',
                'description' => 'Layanan renovasi bangunan mencakup perbaikan struktur, penambahan ruangan, perubahan layout, dan pembaruan tampilan. Kami memastikan proses renovasi berjalan lancar tanpa mengganggu aktivitas Anda. Konsultasi gratis untuk estimasi biaya dan timeline pengerjaan.',
                'icon' => 'fas fa-tools',
                'price_start' => 850000,
                'price_unit' => 'per meter',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Konstruksi Baja & Las',
                'short_description' => 'Jasa pengelasan dan fabrikasi baja untuk kebutuhan konstruksi, kanopi, pagar, dan struktur baja lainnya.',
                'description' => 'Spesialisasi dalam pekerjaan las dan fabrikasi baja meliputi pembuatan kanopi, pagar, tangga baja, railing, konstruksi baja ringan, dan struktur baja berat. Menggunakan teknologi pengelasan modern dengan hasil yang kuat, rapi, dan tahan lama. Sertifikasi welder internasional.',
                'icon' => 'fas fa-fire',
                'price_start' => 25000,
                'price_unit' => 'per kg',
                'is_featured' => true,
                'sort_order' => 3,
            ],

            [
                'name' => 'Konstruksi Komersial',
                'short_description' => 'Pembangunan gedung komersial seperti ruko, kantor, gudang, dan kawasan industri.',
                'description' => 'Layanan konstruksi komersial lengkap untuk pembangunan ruko, gedung perkantoran, gudang, pabrik, dan fasilitas komersial lainnya. Kami menangani proyek dari skala kecil hingga besar dengan standar keamanan dan kualitas tinggi. Dilengkapi dengan manajemen proyek profesional.',
                'icon' => 'fas fa-building',
                'price_start' => 850000,
                'price_unit' => 'per meter',
                'is_featured' => true,
                'sort_order' => 5,
            ],

        ];

        foreach ($services as $service) {
            Service::create(array_merge($service, [
                'slug' => Str::slug($service['name']),
            ]));
        }
    }
}
