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
                'price_start' => 3500000,
                'price_unit' => 'per m²',
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Renovasi Bangunan',
                'short_description' => 'Renovasi dan perbaikan bangunan untuk memperbarui tampilan serta meningkatkan fungsi ruang.',
                'description' => 'Layanan renovasi bangunan mencakup perbaikan struktur, penambahan ruangan, perubahan layout, dan pembaruan tampilan. Kami memastikan proses renovasi berjalan lancar tanpa mengganggu aktivitas Anda. Konsultasi gratis untuk estimasi biaya dan timeline pengerjaan.',
                'icon' => 'fas fa-tools',
                'price_start' => 2000000,
                'price_unit' => 'per m²',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Konstruksi Baja & Las',
                'short_description' => 'Jasa pengelasan dan fabrikasi baja untuk kebutuhan konstruksi, kanopi, pagar, dan struktur baja lainnya.',
                'description' => 'Spesialisasi dalam pekerjaan las dan fabrikasi baja meliputi pembuatan kanopi, pagar, tangga baja, railing, konstruksi baja ringan, dan struktur baja berat. Menggunakan teknologi pengelasan modern dengan hasil yang kuat, rapi, dan tahan lama. Sertifikasi welder internasional.',
                'icon' => 'fas fa-fire',
                'price_start' => 500000,
                'price_unit' => 'per meter',
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Desain Interior',
                'short_description' => 'Jasa desain interior profesional untuk menciptakan ruangan yang fungsional dan estetis.',
                'description' => 'Tim desainer interior kami akan merancang ruangan Anda agar tampil menawan dan fungsional. Layanan meliputi konsultasi desain, pembuatan konsep 3D, pemilihan material dan furnitur, serta pengawasan pelaksanaan. Tersedia berbagai gaya: minimalis, modern, industrial, dan klasik.',
                'icon' => 'fas fa-couch',
                'price_start' => 1500000,
                'price_unit' => 'per m²',
                'is_featured' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Konstruksi Komersial',
                'short_description' => 'Pembangunan gedung komersial seperti ruko, kantor, gudang, dan kawasan industri.',
                'description' => 'Layanan konstruksi komersial lengkap untuk pembangunan ruko, gedung perkantoran, gudang, pabrik, dan fasilitas komersial lainnya. Kami menangani proyek dari skala kecil hingga besar dengan standar keamanan dan kualitas tinggi. Dilengkapi dengan manajemen proyek profesional.',
                'icon' => 'fas fa-building',
                'price_start' => 5000000,
                'price_unit' => 'per m²',
                'is_featured' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Perawatan & Perbaikan',
                'short_description' => 'Layanan perawatan rutin dan perbaikan kerusakan bangunan secara cepat dan profesional.',
                'description' => 'Layanan maintenance dan perbaikan bangunan mencakup perbaikan atap bocor, dinding retak, instalasi listrik, plumbing, pengecatan ulang, dan perawatan rutin lainnya. Respon cepat dan harga terjangkau. Tersedia paket perawatan berkala untuk hunian dan gedung komersial.',
                'icon' => 'fas fa-wrench',
                'price_start' => 300000,
                'price_unit' => 'per hari',
                'is_featured' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($services as $service) {
            Service::create(array_merge($service, [
                'slug' => Str::slug($service['name']),
            ]));
        }
    }
}
