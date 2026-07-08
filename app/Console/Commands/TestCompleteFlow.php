<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderUpdate;
use App\Models\Service;
use App\Models\User;
use App\Services\CustomerNotificationService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TestCompleteFlow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:complete-flow {--real-notifications : Kirim email dan WhatsApp asli (jika kredensial ada)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit kredensial dan uji alur lengkap sistem secara step-by-step';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->title('WeldTrack Integration & Configuration Audit Tool');

        // ==========================================
        // AUDIT CONFIGURATION
        // ==========================================
        $this->auditConfig();

        if (!$this->confirm('Apakah Anda ingin melanjutkan ke simulasi alur lengkap?', true)) {
            $this->info('Pengujian dibatalkan.');
            return 0;
        }

        $realNotifications = $this->option('real-notifications');
        if (!$realNotifications) {
            $this->comment('Catatan: Pengujian menggunakan simulasi log notifikasi. Gunakan opsi --real-notifications untuk mencoba kirim asli.');
        }

        // ==========================================
        // STEP 1: GOOGLE SIGN-IN SIMULATION
        // ==========================================
        $this->section('Step 1: Google Authentication / Registration Simulation');
        $email = $this->ask('Masukkan email untuk Google login test', 'tester-google@gmail.com');
        $name = $this->ask('Masukkan nama untuk Google user', 'Google Tester User');

        $this->info("Mensimulasikan callback Socialite untuk: {$email}");
        
        $user = User::where('email', $email)->first();
        if ($user) {
            $this->comment("User sudah ada di database, menggunakan user existing (ID: {$user->id}).");
            $user->update([
                'provider_name' => 'google',
                'provider_id' => 'simulated-google-id-' . Str::random(10),
            ]);
        } else {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'role' => User::ROLE_CUSTOMER,
                'provider_name' => 'google',
                'provider_id' => 'simulated-google-id-' . Str::random(10),
                'provider_token' => 'simulated-token-' . Str::random(20),
            ]);
            $this->info("User baru berhasil dibuat (ID: {$user->id}) dengan role: customer.");
        }

        // ==========================================
        // STEP 2: CREATE ORDER
        // ==========================================
        $this->section('Step 2: Submit New Order (Pesan Layanan)');
        
        $service = Service::where('is_active', true)->first();
        if (!$service) {
            $this->comment("Tidak ada layanan aktif. Membuat layanan contoh: Pagar Minimalis...");
            $service = Service::create([
                'name' => 'Pagar Minimalis',
                'slug' => 'pagar-minimalis',
                'description' => 'Layanan pembuatan pagar besi minimalis berkualitas.',
                'short_description' => 'Pagar minimalis premium.',
                'icon' => 'fas fa-shield-alt',
                'price_start' => 500000,
                'price_unit' => 'm2',
                'is_featured' => true,
                'is_active' => true,
            ]);
        }

        $this->info("Menggunakan Layanan: {$service->name}");
        $phone = $this->ask('Masukkan nomor WhatsApp pemesan (Format: 08xx atau 62xx)', '081234567890');
        $address = $this->ask('Masukkan alamat proyek', 'Jl. Testing No. 123, Cikarang');

        if (!$realNotifications) {
            Mail::fake();
            $this->comment('[Mail::fake() diaktifkan untuk simulasi]');
        }

        $orderNumber = Order::generateOrderNumber();
        $order = Order::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'order_number' => $orderNumber,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $phone,
            'address' => $address,
            'description' => 'Simulasi order otomatis untuk pengujian integrasi.',
            'status' => Order::determineInitialStatus(),
            'preferred_consultation_date' => now()->addDays(2),
            'preferred_consultation_time' => '09:00 - 11:00 (Pagi)',
            'notes' => 'Harap dihubungi via WA.',
        ]);

        $this->info("Pesanan Berhasil Dibuat!");
        $this->line("Nomor Pesanan: <fg=cyan>{$order->order_number}</>");
        $this->line("Status Awal  : <fg=yellow>{$order->status_label} ({$order->status})</>");

        // Kirim email notifikasi pesanan baru ke admin
        try {
            Mail::to(config('app.admin_email'))->send(new \App\Mail\NewOrderNotification($order->load('service')));
            if ($realNotifications) {
                $this->info("Email notifikasi pesanan baru dikirim ke Admin: " . config('app.admin_email'));
            } else {
                $this->info("Email notifikasi pesanan baru disimulasikan sukses.");
            }
        } catch (\Throwable $e) {
            $this->error("Gagal mengirim email admin: " . $e->getMessage());
        }

        // ==========================================
        // STEP 3: ADMIN UPDATE & FOREMAN ASSIGNMENT
        // ==========================================
        $this->section('Step 3: Admin Action (Jadwalkan Konsultasi & Tunjuk Mandor)');

        // Dapatkan/buat Foreman
        $foreman = User::where('role', User::ROLE_FOREMAN)->first();
        if (!$foreman) {
            $this->comment("Tidak ada Mandor (Foreman) di database. Membuat user mandor baru...");
            $foreman = User::create([
                'name' => 'Mandor Joni',
                'email' => 'joni.mandor@weldtrack.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_FOREMAN,
                'phone' => '087777777777',
                'address' => 'Bekasi, Jawa Barat',
            ]);
            $this->info("Mandor baru berhasil dibuat (ID: {$foreman->id}): {$foreman->name}");
        } else {
            $this->info("Menggunakan Mandor existing: {$foreman->name} (ID: {$foreman->id})");
        }

        $this->line('Mengubah status pesanan ke: scheduled (Konsultasi Dijadwalkan)');
        $order->update([
            'status' => Order::STATUS_SCHEDULED,
            'foreman_id' => $foreman->id,
            'consultation_date' => now()->addDays(3),
            'consultation_time' => '10:00 WIB',
            'consultation_place' => 'Lokasi Proyek Pemesan',
            'admin_notes' => 'Jadwal survei disetujui. Mandor ditugaskan.',
        ]);

        $this->line("Status Baru: <fg=blue>{$order->status_label}</>");

        // ==========================================
        // STEP 4: FOREMAN PROGRESS UPDATE
        // ==========================================
        $this->section('Step 4: Foreman Action (Kirim Update Lapangan)');

        // Buat mock photo file di public/uploads/order-updates jika belum ada
        $directory = public_path('uploads/order-updates');
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        $mockPhotoPath = 'uploads/order-updates/mock-test-image.png';
        if (!File::exists(public_path($mockPhotoPath))) {
            // Buat gambar 1x1 pixel kosong
            $img = imagecreatetruecolor(1, 1);
            imagepng($img, public_path($mockPhotoPath));
            imagedestroy($img);
        }

        $this->line('Mandor membuat update lapangan: Progres 50%...');
        
        $update = OrderUpdate::create([
            'order_id' => $order->id,
            'user_id' => $foreman->id,
            'title' => 'Pemasangan Rangka Pagar',
            'description' => 'Rangka pagar utama selesai dilas dan dipasang di lokasi. Siap untuk proses pengecatan.',
            'progress_percent' => 50,
            'photo_path' => $mockPhotoPath,
            'update_date' => now(),
            'status_after_update' => Order::STATUS_IN_PROGRESS,
            'is_visible_to_customer' => true,
        ]);

        // Update status order utama ke in_progress
        $order->update(['status' => Order::STATUS_IN_PROGRESS]);

        $this->info('Update lapangan berhasil disimpan.');
        $this->line("Status Order Utama sekarang: <fg=magenta>{$order->status_label}</>");

        // ==========================================
        // STEP 5: NOTIFICATIONS DISPATCH
        // ==========================================
        $this->section('Step 5: Dispatch Notifications to Customer');

        if (!$realNotifications) {
            // Reset Mail fake agar kita bisa melihat log internal, atau lakukan test tersendiri
            // Di sini kita langsung panggil notifier service secara terisolasi tanpa fake untuk melihat hasilnya
            $this->comment('Mengirim notifikasi customer (Simulasi Log)...');
            
            // Kita jalankan notifikasi real tetapi override Mail dan Fonnte config secara runtime agar tidak terkirim asli
            config(['mail.default' => 'log']);
            config(['services.fonnte.token' => '']); // Supaya menulis ke log
            
            $notifier = new CustomerNotificationService();
            $notifier->notifyOrderUpdate($order, $update);
            
            $this->info('Notifikasi berhasil diproses! Silakan cek storage/logs/laravel.log untuk detail output log.');
        } else {
            $this->comment('Mengirim notifikasi customer via SMTP & Fonnte WhatsApp...');
            $notifier = new CustomerNotificationService();
            $notifier->notifyOrderUpdate($order, $update);
            $this->info('Proses pengiriman selesai.');
        }

        $this->section('Simulation Summary', 'info');
        $this->table(
            ['Parameter', 'Nilai'],
            [
                ['Nomor Pesanan', $order->order_number],
                ['Nama Pelanggan', $order->name],
                ['Email Pelanggan', $order->email],
                ['Nomor WhatsApp', $order->phone],
                ['Status Akhir', $order->status_label],
                ['Mandor Ditugaskan', $foreman->name],
                ['Progres Proyek', $update->progress_percent . '%'],
            ]
        );

        $this->info('Simulasi Alur Lengkap Berhasil Selesai!');
        return 0;
    }

    /**
     * Helper to print title.
     */
    protected function title(string $text)
    {
        $this->line('');
        $this->line(str_repeat('=', 60));
        $this->line(' ' . $text);
        $this->line(str_repeat('=', 60));
    }

    /**
     * Helper to print section header.
     */
    protected function section(string $text, string $style = 'comment')
    {
        $this->line('');
        $this->line("<{$style}>>>> {$text}</{$style}>");
        $this->line(str_repeat('-', 40));
    }

    /**
     * Audit configuration settings in .env/config
     */
    protected function auditConfig()
    {
        $this->section('Configuration & Credentials Audit');

        // 1. Google OAuth
        $googleClientId = config('services.google.client_id');
        $googleClientSecret = config('services.google.client_secret');
        $googleRedirect = config('services.google.redirect');

        $googleStatus = 'Mati / Kosong';
        $googleColor = 'error';
        if (filled($googleClientId) && filled($googleClientSecret)) {
            $googleStatus = 'Terkonfigurasi';
            $googleColor = 'info';
        }

        // 2. Email SMTP
        $mailMailer = config('mail.default');
        $mailHost = config('mail.mailers.smtp.host');
        $mailUsername = config('mail.mailers.smtp.username');

        $mailStatus = 'Mati / Log';
        $mailColor = 'comment';
        if ($mailMailer === 'smtp' && filled($mailHost) && filled($mailUsername)) {
            $mailStatus = 'SMTP Aktif (' . $mailHost . ')';
            $mailColor = 'info';
        }

        // 3. Fonnte WhatsApp
        $fonnteToken = config('services.fonnte.token');
        $fonnteStatus = 'Mati / Simulasi Log';
        $fonnteColor = 'comment';
        if (filled($fonnteToken)) {
            $fonnteStatus = 'Terkonfigurasi (Aktif)';
            $fonnteColor = 'info';
        }

        $this->table(
            ['Komponen', 'Status', 'Detail Config'],
            [
                ['Google OAuth Login', "<{$googleColor}>{$googleStatus}</{$googleColor}>", "Client ID: " . Str::limit($googleClientId ?? '-', 20)],
                ['Email Notifikasi', "<{$mailColor}>{$mailStatus}</{$mailColor}>", "Username: " . ($mailUsername ?? '-')],
                ['WhatsApp Fonnte', "<{$fonnteColor}>{$fonnteStatus}</{$fonnteColor}>", "Token: " . Str::limit($fonnteToken ?? '-', 8)],
            ]
        );
    }
}
