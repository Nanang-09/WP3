<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'name' => 'Ahmad Wijaya',
                'role' => 'Pemilik Rumah',
                'content' => 'Sangat puas dengan hasil kerja WeldTrack! Rumah saya dibangun dengan sangat baik, tepat waktu, dan sesuai budget. Tim nya sangat profesional dan komunikatif selama proses pembangunan.',
                'rating' => 5,
            ],
            [
                'name' => 'Siti Rahayu',
                'role' => 'Direktur PT. Maju Jaya',
                'content' => 'Renovasi kantor kami berjalan sangat lancar. WeldTrack memberikan solusi desain yang inovatif dan pelaksanaan yang rapi. Karyawan kami sangat senang dengan kantor baru ini.',
                'rating' => 5,
            ],
            [
                'name' => 'Budi Santoso',
                'role' => 'Manager Operasional',
                'content' => 'Konstruksi baja gudang kami sangat kuat dan presisi. Tim welder WeldTrack benar-benar terampil dan berpengalaman. Proyek selesai lebih cepat dari jadwal yang ditentukan.',
                'rating' => 5,
            ],
            [
                'name' => 'Diana Putri',
                'role' => 'Pemilik Restoran',
                'content' => 'Interior restoran kami tampil memukau berkat sentuhan desain WeldTrack. Pelanggan selalu memuji suasana restoran kami. Investasi yang sangat berharga!',
                'rating' => 5,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create($testimonial);
        }
    }
}
