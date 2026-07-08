<?php

namespace App\Services;

use App\Models\Order;
use App\Mail\NewOrderNotification;
use App\Mail\CustomerOrderReceivedNotification;
use App\Mail\CustomerConsultationScheduledNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected $waService;

    public function __construct(WhatsAppService $waService)
    {
        $this->waService = $waService;
    }

    /**
     * Kirim pesan WA ke nomor admin utama + nomor secondary (CC) jika dikonfigurasi.
     */
    protected function sendToAdminAndCC(string $message): void
    {
        $adminWa     = config('app.admin_whatsapp');
        $secondaryWa = config('app.secondary_whatsapp');

        if ($adminWa) {
            $this->waService->sendMessage($adminWa, $message);
        }

        if ($secondaryWa && $secondaryWa !== $adminWa) {
            $this->waService->sendMessage($secondaryWa, $message);
        }
    }

    /**
     * Kirim pesan WA dengan tombol ke nomor admin utama + nomor secondary (CC) jika dikonfigurasi.
     */
    protected function sendButtonToAdminAndCC(string $message, array $buttons): void
    {
        $adminWa     = config('app.admin_whatsapp');
        $secondaryWa = config('app.secondary_whatsapp');

        if ($adminWa) {
            $this->waService->sendButtonMessage($adminWa, $message, $buttons);
        }

        if ($secondaryWa && $secondaryWa !== $adminWa) {
            $this->waService->sendButtonMessage($secondaryWa, $message, $buttons);
        }
    }

    public function notifyNewOrder(Order $order)
    {
        $adminEmail = config('app.admin_email');

        // --- 1. NOTIFIKASI ADMIN ---
        // Email ke Admin
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new NewOrderNotification($order));
            } catch (\Exception $e) {
                Log::warning('Gagal kirim email pesanan baru ke admin: ' . $e->getMessage());
            }
        }

        // Inisialisasi sesi bot WA Admin di cache
        $adminWa = config('app.admin_whatsapp');
        if ($adminWa) {
            $cleanAdmin = preg_replace('/\D+/', '', $adminWa);
            if (str_starts_with($cleanAdmin, '0')) {
                $cleanAdmin = '62' . substr($cleanAdmin, 1);
            }
            if (!empty($cleanAdmin) && !str_starts_with($cleanAdmin, '62')) {
                $cleanAdmin = '62' . $cleanAdmin;
            }

            $sessionKey = "wa_bot_session_{$cleanAdmin}";
            cache()->put($sessionKey, [
                'state'        => 'AWAITING_CONFIRMATION',
                'order_number' => $order->order_number,
            ], now()->addMinutes(60));
        }

        // WhatsApp ke Admin + CC (dilengkapi tombol opsi tindakan langsung)
        $adminOrderUrl = route('admin.orders.show', $order);
        $message  = "*🔔 Pesanan Baru Diterima!*\n\n";
        $message .= "No Pesanan: *{$order->order_number}*\n";
        $message .= "Nama Pelanggan: {$order->name}\n";
        $message .= "No HP: {$order->phone}\n";
        $message .= "Alamat: {$order->address}\n";
        $message .= "Deskripsi: {$order->description}\n\n";
        $message .= "👉 *Buka detail pesanan:*\n{$adminOrderUrl}\n\n";
        $message .= "Silakan pilih tindakan langsung melalui tombol di bawah:";

        $this->sendButtonToAdminAndCC($message, ['✅ Setujui', '❌ Tolak']);

        // --- 2. NOTIFIKASI PELANGGAN ---
        // Email ke Pelanggan
        if ($order->email) {
            try {
                Mail::to($order->email)->send(new CustomerOrderReceivedNotification($order));
            } catch (\Exception $e) {
                Log::warning('Gagal kirim email tanda terima ke pelanggan: ' . $e->getMessage());
            }
        }

        // WhatsApp ke Pelanggan — Rincian Pesanan Lengkap
        if ($order->phone) {
            $orderDate   = $order->preferred_consultation_date?->translatedFormat('d F Y') ?? '-';
            $orderTime   = $order->preferred_consultation_time ?? '-';
            $trackingUrl = route('order.index');

            $custMessage  = "*✅ Pesanan Anda Berhasil Diterima!*\n\n";
            $custMessage .= "Halo *{$order->name}*,\n\n";
            $custMessage .= "Terima kasih telah memesan layanan di *" . config('app.business_name') . "*! Berikut ringkasan pesanan Anda:\n\n";
            $custMessage .= "📋 *No. Pesanan:* {$order->order_number}\n";
            $custMessage .= "🔧 *Layanan:* " . ($order->service->name ?? '-') . "\n";
            $custMessage .= "📍 *Alamat:* {$order->address}\n";
            $custMessage .= "📅 *Usulan Konsultasi:* {$orderDate}, {$orderTime}\n";
            if ($order->budget_range) {
                $custMessage .= "💰 *Estimasi Budget:* {$order->budget_range}\n";
            }
            $custMessage .= "\n---\n";
            $custMessage .= "⏳ Admin kami sedang memproses pesanan ini dan akan segera menghubungi Anda untuk *konfirmasi jadwal konsultasi & survei lokasi*.\n\n";
            $custMessage .= "📱 Pantau status pesanan Anda secara real-time di:\n{$trackingUrl}\n\n";
            $custMessage .= "_Simpan pesan ini sebagai bukti pemesanan Anda. Terima kasih! 🙏_";

            $this->waService->sendMessage($order->phone, $custMessage);
        }
    }

    public function notifyCustomerConsultationScheduled(Order $order)
    {
        // Email ke Pelanggan
        if ($order->email) {
            try {
                Mail::to($order->email)->send(new CustomerConsultationScheduledNotification($order));
            } catch (\Exception $e) {
                Log::warning('Gagal kirim email jadwal konsultasi ke pelanggan: ' . $e->getMessage());
            }
        }

        // WhatsApp ke Pelanggan
        if ($order->phone) {
            $consultDateStr = $order->consultation_date ? $order->consultation_date->translatedFormat('d F Y') : '-';
            $message  = "*📅 Jadwal Konsultasi & Survei WeldTrack*\n\n";
            $message .= "Halo {$order->name},\n\n";
            $message .= "Admin kami telah menetapkan jadwal konsultasi fisik dan survei lokasi untuk pesanan Anda (*{$order->order_number}*).\n\n";
            $message .= "📅 Tanggal: {$consultDateStr}\n";
            $message .= "⏰ Waktu: {$order->consultation_time}\n";
            $message .= "📍 Tempat: " . ($order->consultation_place ?: 'Lokasi Proyek') . "\n\n";
            $message .= "Mohon pastikan Anda berada di lokasi pada waktu tersebut. Tim kami akan hadir untuk melakukan pengukuran lapangan.";

            $this->waService->sendMessage($order->phone, $message);
        }

        // CC ke secondary WA (notif bahwa admin sudah jadwalkan konsultasi)
        $adminOrderUrl  = route('admin.orders.show', $order);
        $consultDateStr = $order->consultation_date ? $order->consultation_date->translatedFormat('d F Y') : '-';
        $ccMessage  = "*✅ Jadwal Konsultasi Ditetapkan*\n\n";
        $ccMessage .= "Pesanan *{$order->order_number}* ({$order->name}) telah dijadwalkan:\n";
        $ccMessage .= "📅 {$consultDateStr}, ⏰ {$order->consultation_time}\n";
        $ccMessage .= "📍 " . ($order->consultation_place ?: 'Lokasi Proyek') . "\n\n";
        $ccMessage .= "👉 *Buka detail:* {$adminOrderUrl}";

        $this->sendToAdminAndCC($ccMessage);
    }

    public function notifyCustomerProjectStarted(Order $order)
    {
        if ($order->phone) {
            $message  = "*🚀 Proyek Dimulai!*\n\n";
            $message .= "Halo {$order->name},\n\n";
            $message .= "Proyek pesanan Anda ({$order->order_number}) telah resmi dijadwalkan dan ditugaskan kepada Mandor.\n";
            $message .= "Anda bisa memantau perkembangan secara berkala melalui menu Progres.\n\n";
            $message .= "Terima kasih atas kepercayaan Anda pada CV Sunrise Island.";

            $this->waService->sendMessage($order->phone, $message);
        }

        // CC notif ke secondary WA
        $adminOrderUrl = route('admin.orders.show', $order);
        $ccMessage  = "*🚀 Proyek Dimulai*\n\n";
        $ccMessage .= "Pesanan *{$order->order_number}* ({$order->name}) telah masuk tahap pengerjaan.\n";
        $ccMessage .= "Mandor: " . ($order->foreman->name ?? '-') . "\n\n";
        $ccMessage .= "👉 *Buka detail:* {$adminOrderUrl}";

        $this->sendToAdminAndCC($ccMessage);
    }

    public function notifyForemanAssigned(Order $order)
    {
        $foreman = $order->foreman;
        if ($foreman && $foreman->phone) {
            $startDateStr = $order->project_start_date ? $order->project_start_date->translatedFormat('d F Y') : '-';
            $endDateStr   = $order->project_end_date   ? $order->project_end_date->translatedFormat('d F Y')   : '-';
            $message  = "*🔧 Penugasan Proyek Baru!*\n\n";
            $message .= "Halo {$foreman->name},\n\n";
            $message .= "Anda telah ditugaskan untuk proyek baru ({$order->order_number}).\n";
            $message .= "Pelanggan: {$order->name}\n";
            $message .= "Tgl Mulai: {$startDateStr}\n";
            $message .= "Tgl Selesai: {$endDateStr}\n\n";
            $message .= "Silakan cek dashboard mandor untuk melihat rincian bahan (panjang, bentuk) dan spesifikasi agar dapat disiapkan sesuai kebutuhan pelanggan (menghindari bahan mubazir).";

            $this->waService->sendMessage($foreman->phone, $message);
        }
    }
}
