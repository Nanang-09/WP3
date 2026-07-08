<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\NotificationService;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    protected WhatsAppService $waService;
    protected NotificationService $notificationService;

    // ─── Bot States ────────────────────────────────────────────────────────────
    const STATE_AWAITING_CONFIRMATION  = 'AWAITING_CONFIRMATION';
    const STATE_AWAITING_DATE          = 'AWAITING_DATE';
    const STATE_AWAITING_CUSTOM_DATE   = 'AWAITING_CUSTOM_DATE';
    const STATE_AWAITING_TIME          = 'AWAITING_TIME';
    const STATE_AWAITING_CUSTOM_TIME   = 'AWAITING_CUSTOM_TIME';
    const STATE_AWAITING_PLACE         = 'AWAITING_PLACE';
    const STATE_AWAITING_CUSTOM_PLACE  = 'AWAITING_CUSTOM_PLACE';
    const STATE_AWAITING_REJECT_REASON = 'AWAITING_REJECT_REASON';

    const SESSION_TTL_MINUTES = 60;

    public function __construct(WhatsAppService $waService, NotificationService $notificationService)
    {
        $this->waService = $waService;
        $this->notificationService = $notificationService;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ENTRY POINT
    // ─────────────────────────────────────────────────────────────────────────
    public function handle(Request $request): \Illuminate\Http\JsonResponse
    {
        $sender  = $request->input('sender', '');
        $message = trim($request->input('message', ''));

        Log::info("Webhook WA dari {$sender}: {$message}");

        if (!$sender || empty($message)) {
            return response()->json(['status' => 'ignored', 'reason' => 'empty_input']);
        }

        $cleanSender = $this->normalizePhone($sender);
        $adminPhone  = config('app.admin_whatsapp', '');
        $cleanAdmin  = $this->normalizePhone($adminPhone);

        // Hanya proses pesan dari nomor admin
        if ($cleanSender !== $cleanAdmin) {
            Log::info("Diabaikan: pengirim ({$cleanSender}) bukan admin ({$cleanAdmin})");
            return response()->json(['status' => 'ignored', 'reason' => 'unauthorized']);
        }

        $sessionKey = "wa_bot_session_{$cleanAdmin}";
        $session    = cache()->get($sessionKey);
        $state      = $session['state'] ?? null;

        // ── Perintah global: BATAL (reset sesi) ──────────────────────────────
        if (strtoupper(trim($message)) === 'BATAL') {
            cache()->forget($sessionKey);
            $this->waService->sendMessage($adminPhone,
                "❌ Alur bot dibatalkan.\n\nAnda bisa mengatur jadwal secara manual melalui panel web admin."
            );
            return response()->json(['status' => 'success', 'action' => 'cancelled']);
        }

        // ── Jika tidak ada sesi aktif ─────────────────────────────────────────
        if ($state === null) {
            // Dukung format lama: JADWAL#NoPesanan#...
            if (str_starts_with(strtoupper($message), 'JADWAL')) {
                return $this->handleLegacyJadwal($message, $adminPhone);
            }
            return response()->json(['status' => 'ignored', 'reason' => 'no_active_session']);
        }

        // ── Route berdasarkan state ───────────────────────────────────────────
        return match ($state) {
            self::STATE_AWAITING_CONFIRMATION  => $this->handleConfirmation($message, $session, $adminPhone, $sessionKey),
            self::STATE_AWAITING_DATE          => $this->handleDateSelection($message, $session, $adminPhone, $sessionKey),
            self::STATE_AWAITING_CUSTOM_DATE   => $this->handleCustomDate($message, $session, $adminPhone, $sessionKey),
            self::STATE_AWAITING_TIME          => $this->handleTimeSelection($message, $session, $adminPhone, $sessionKey),
            self::STATE_AWAITING_CUSTOM_TIME   => $this->handleCustomTime($message, $session, $adminPhone, $sessionKey),
            self::STATE_AWAITING_PLACE         => $this->handlePlaceSelection($message, $session, $adminPhone, $sessionKey),
            self::STATE_AWAITING_CUSTOM_PLACE  => $this->handleCustomPlace($message, $session, $adminPhone, $sessionKey),
            self::STATE_AWAITING_REJECT_REASON => $this->handleRejectReason($message, $session, $adminPhone, $sessionKey),
            default                            => $this->handleUnknownState($sessionKey, $adminPhone),
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 1 — Konfirmasi atau Tolak
    // ─────────────────────────────────────────────────────────────────────────
    private function handleConfirmation(string $message, array $session, string $adminPhone, string $sessionKey): \Illuminate\Http\JsonResponse
    {
        $orderNumber = $session['order_number'];

        if (str_contains($message, 'Setujui') || str_contains($message, '✅')) {
            return $this->sendDateOptions($orderNumber, $adminPhone, $sessionKey);
        }

        if (str_contains($message, 'Tolak') || str_contains($message, '❌')) {
            cache()->put($sessionKey, [
                'state'        => self::STATE_AWAITING_REJECT_REASON,
                'order_number' => $orderNumber,
            ], now()->addMinutes(self::SESSION_TTL_MINUTES));

            $this->waService->sendMessage($adminPhone,
                "❌ Ketik *alasan penolakan* untuk pesanan *#{$orderNumber}*:\n\n_(Ketik BATAL untuk membatalkan)_"
            );
            return response()->json(['status' => 'success', 'action' => 'awaiting_reject_reason']);
        }

        $this->waService->sendMessage($adminPhone, "⚠️ Silakan pilih salah satu tombol yang tersedia.");
        return response()->json(['status' => 'ignored', 'reason' => 'unrecognized_input']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 2 — Pilih Tanggal
    // ─────────────────────────────────────────────────────────────────────────
    private function sendDateOptions(string $orderNumber, string $adminPhone, string $sessionKey): \Illuminate\Http\JsonResponse
    {
        // Generate opsi 6 hari ke depan
        $dateOptions = [];
        for ($i = 1; $i <= 6; $i++) {
            $date  = now()->addDays($i);
            $dayLabel = match ($i) {
                1 => 'Besok',
                2 => 'Lusa',
                default => $date->format('D'), // Mon, Tue, etc.
            };
            $label = "📅 {$dayLabel} {$date->format('d/m')}";
            $dateOptions[$label] = $date->format('Y-m-d');
        }
        $dateOptions['✏️ Tanggal Lain'] = 'custom';

        cache()->put($sessionKey, [
            'state'        => self::STATE_AWAITING_DATE,
            'order_number' => $orderNumber,
            'date_options' => $dateOptions,
        ], now()->addMinutes(self::SESSION_TTL_MINUTES));

        $keys = array_keys($dateOptions);

        // Kirim batch pertama (3 tombol)
        $msg1 = "📅 Pilih *TANGGAL* konsultasi untuk pesanan *#{$orderNumber}*:";
        $this->waService->sendButtonMessage($adminPhone, $msg1, array_slice($keys, 0, 3));

        // Kirim batch kedua (3-4 tombol: hari 4-6 + Tanggal Lain)
        $this->waService->sendButtonMessage($adminPhone, "Atau pilih tanggal lain:", array_slice($keys, 3));

        return response()->json(['status' => 'success', 'action' => 'awaiting_date']);
    }

    private function handleDateSelection(string $message, array $session, string $adminPhone, string $sessionKey): \Illuminate\Http\JsonResponse
    {
        $dateOptions = $session['date_options'] ?? [];
        $orderNumber = $session['order_number'];

        // Admin memilih "Tanggal Lain" — minta ketik manual
        if ($message === '✏️ Tanggal Lain') {
            cache()->put($sessionKey, array_merge($session, [
                'state' => self::STATE_AWAITING_CUSTOM_DATE,
            ]), now()->addMinutes(self::SESSION_TTL_MINUTES));

            $this->waService->sendMessage($adminPhone,
                "✏️ Ketik tanggal konsultasi dalam format *DD-MM-YYYY*:\nContoh: 15-07-2026\n\n_(Ketik BATAL untuk membatalkan)_"
            );
            return response()->json(['status' => 'success', 'action' => 'awaiting_custom_date']);
        }

        // Admin memilih salah satu tombol tanggal
        if (isset($dateOptions[$message])) {
            $selectedDate = $dateOptions[$message];
            return $this->sendTimeOptions($orderNumber, $selectedDate, $adminPhone, $sessionKey);
        }

        // Input tidak dikenali
        $this->waService->sendMessage($adminPhone, "⚠️ Pilih salah satu tombol tanggal yang tersedia.");
        return response()->json(['status' => 'ignored', 'reason' => 'unrecognized_date']);
    }

    private function handleCustomDate(string $message, array $session, string $adminPhone, string $sessionKey): \Illuminate\Http\JsonResponse
    {
        $orderNumber = $session['order_number'];

        try {
            // Coba berbagai format: DD-MM-YYYY, DD/MM/YYYY, YYYY-MM-DD
            $date = null;
            foreach (['d-m-Y', 'd/m/Y', 'Y-m-d'] as $format) {
                $parsed = \DateTime::createFromFormat($format, $message);
                if ($parsed && $parsed->format($format) === $message) {
                    $date = Carbon::instance($parsed);
                    break;
                }
            }

            if (!$date) {
                throw new \Exception("Format tidak valid");
            }

            $selectedDate = $date->format('Y-m-d');
        } catch (\Exception $e) {
            $this->waService->sendMessage($adminPhone,
                "❌ Format tanggal tidak valid.\nGunakan format: *DD-MM-YYYY*\nContoh: 15-07-2026"
            );
            return response()->json(['status' => 'error', 'reason' => 'invalid_date_format']);
        }

        return $this->sendTimeOptions($orderNumber, $selectedDate, $adminPhone, $sessionKey);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 3 — Pilih Waktu
    // ─────────────────────────────────────────────────────────────────────────
    private function sendTimeOptions(string $orderNumber, string $selectedDate, string $adminPhone, string $sessionKey): \Illuminate\Http\JsonResponse
    {
        $displayDate = Carbon::parse($selectedDate)->translatedFormat('d F Y');

        $timeOptions = [
            '⏰ 08:00-10:00' => '08:00 - 10:00 (Pagi)',
            '⏰ 10:00-12:00' => '10:00 - 12:00 (Siang)',
            '⏰ 13:00-15:00' => '13:00 - 15:00 (Sore)',
            '⏰ 15:00-17:00' => '15:00 - 17:00 (Sore Akhir)',
            '✏️ Waktu Lain'  => 'custom',
        ];

        cache()->put($sessionKey, [
            'state'        => self::STATE_AWAITING_TIME,
            'order_number' => $orderNumber,
            'date'         => $selectedDate,
            'time_options' => $timeOptions,
        ], now()->addMinutes(self::SESSION_TTL_MINUTES));

        $keys = array_keys($timeOptions);

        $msg = "✅ Tanggal: *{$displayDate}*\n\n⏰ Pilih *WAKTU* konsultasi:";
        $this->waService->sendButtonMessage($adminPhone, $msg, array_slice($keys, 0, 3));
        $this->waService->sendButtonMessage($adminPhone, "Atau waktu lain:", array_slice($keys, 3));

        return response()->json(['status' => 'success', 'action' => 'awaiting_time']);
    }

    private function handleTimeSelection(string $message, array $session, string $adminPhone, string $sessionKey): \Illuminate\Http\JsonResponse
    {
        $timeOptions = $session['time_options'] ?? [];
        $orderNumber = $session['order_number'];
        $selectedDate = $session['date'];

        if ($message === '✏️ Waktu Lain') {
            cache()->put($sessionKey, array_merge($session, [
                'state' => self::STATE_AWAITING_CUSTOM_TIME,
            ]), now()->addMinutes(self::SESSION_TTL_MINUTES));

            $this->waService->sendMessage($adminPhone,
                "✏️ Ketik waktu konsultasi:\nContoh: 09:00 - 11:00\n\n_(Ketik BATAL untuk membatalkan)_"
            );
            return response()->json(['status' => 'success', 'action' => 'awaiting_custom_time']);
        }

        if (isset($timeOptions[$message])) {
            $selectedTime = $timeOptions[$message];
            return $this->sendPlaceOptions($orderNumber, $selectedDate, $selectedTime, $adminPhone, $sessionKey);
        }

        $this->waService->sendMessage($adminPhone, "⚠️ Pilih salah satu tombol waktu yang tersedia.");
        return response()->json(['status' => 'ignored', 'reason' => 'unrecognized_time']);
    }

    private function handleCustomTime(string $message, array $session, string $adminPhone, string $sessionKey): \Illuminate\Http\JsonResponse
    {
        $selectedTime = trim($message);
        if (empty($selectedTime)) {
            $this->waService->sendMessage($adminPhone, "❌ Waktu tidak boleh kosong. Ketik waktu konsultasi:");
            return response()->json(['status' => 'error', 'reason' => 'empty_time']);
        }
        return $this->sendPlaceOptions($session['order_number'], $session['date'], $selectedTime, $adminPhone, $sessionKey);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 4 — Pilih Tempat
    // ─────────────────────────────────────────────────────────────────────────
    private function sendPlaceOptions(string $orderNumber, string $selectedDate, string $selectedTime, string $adminPhone, string $sessionKey): \Illuminate\Http\JsonResponse
    {
        $placeOptions = [
            '📍 Lokasi Proyek' => 'Lokasi Proyek',
            '🏢 Kantor'        => 'Kantor',
            '✏️ Tempat Lain'   => 'custom',
        ];

        cache()->put($sessionKey, [
            'state'         => self::STATE_AWAITING_PLACE,
            'order_number'  => $orderNumber,
            'date'          => $selectedDate,
            'time'          => $selectedTime,
            'place_options' => $placeOptions,
        ], now()->addMinutes(self::SESSION_TTL_MINUTES));

        $msg = "✅ Waktu: *{$selectedTime}*\n\n📍 Pilih *TEMPAT* konsultasi:";
        $this->waService->sendButtonMessage($adminPhone, $msg, array_keys($placeOptions));

        return response()->json(['status' => 'success', 'action' => 'awaiting_place']);
    }

    private function handlePlaceSelection(string $message, array $session, string $adminPhone, string $sessionKey): \Illuminate\Http\JsonResponse
    {
        $placeOptions = $session['place_options'] ?? [];

        if ($message === '✏️ Tempat Lain') {
            cache()->put($sessionKey, array_merge($session, [
                'state' => self::STATE_AWAITING_CUSTOM_PLACE,
            ]), now()->addMinutes(self::SESSION_TTL_MINUTES));

            $this->waService->sendMessage($adminPhone,
                "✏️ Ketik nama tempat / alamat lokasi konsultasi:\n\n_(Ketik BATAL untuk membatalkan)_"
            );
            return response()->json(['status' => 'success', 'action' => 'awaiting_custom_place']);
        }

        if (isset($placeOptions[$message])) {
            $selectedPlace = $placeOptions[$message];
            return $this->finalizeSchedule(
                $session['order_number'], $session['date'], $session['time'],
                $selectedPlace, $adminPhone, $sessionKey
            );
        }

        $this->waService->sendMessage($adminPhone, "⚠️ Pilih salah satu tombol tempat yang tersedia.");
        return response()->json(['status' => 'ignored', 'reason' => 'unrecognized_place']);
    }

    private function handleCustomPlace(string $message, array $session, string $adminPhone, string $sessionKey): \Illuminate\Http\JsonResponse
    {
        $selectedPlace = trim($message);
        if (empty($selectedPlace)) {
            $this->waService->sendMessage($adminPhone, "❌ Tempat tidak boleh kosong. Ketik nama tempat:");
            return response()->json(['status' => 'error', 'reason' => 'empty_place']);
        }
        return $this->finalizeSchedule(
            $session['order_number'], $session['date'], $session['time'],
            $selectedPlace, $adminPhone, $sessionKey
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // FINALIZE — Update sistem & notifikasi pelanggan
    // ─────────────────────────────────────────────────────────────────────────
    private function finalizeSchedule(string $orderNumber, string $selectedDate, string $selectedTime, string $selectedPlace, string $adminPhone, string $sessionKey): \Illuminate\Http\JsonResponse
    {
        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            cache()->forget($sessionKey);
            $this->waService->sendMessage($adminPhone, "❌ Pesanan *#{$orderNumber}* tidak ditemukan di sistem.");
            return response()->json(['status' => 'error', 'reason' => 'order_not_found']);
        }

        $consultationDate = Carbon::parse($selectedDate);

        // Update database
        $order->update([
            'consultation_date'         => $consultationDate,
            'consultation_time'         => $selectedTime,
            'consultation_place'        => $selectedPlace,
            'status'                    => Order::STATUS_SCHEDULED,
            'is_consultation_confirmed' => true,
        ]);

        // Hapus sesi bot
        cache()->forget($sessionKey);

        // Konfirmasi ke admin
        $adminOrderUrl = route('admin.orders.show', $order);
        $confirmMsg  = "🎉 *Jadwal Konsultasi Berhasil Ditetapkan!*\n\n";
        $confirmMsg .= "📋 Pesanan: *#{$orderNumber}*\n";
        $confirmMsg .= "👤 Pelanggan: {$order->name} ({$order->phone})\n";
        $confirmMsg .= "📅 Tanggal: {$consultationDate->translatedFormat('d F Y')}\n";
        $confirmMsg .= "⏰ Waktu: {$selectedTime}\n";
        $confirmMsg .= "📍 Tempat: {$selectedPlace}\n\n";
        $confirmMsg .= "✉️ Notifikasi otomatis sudah dikirim ke WA & Email pelanggan.\n\n";
        $confirmMsg .= "👉 Buka detail: {$adminOrderUrl}";

        $this->waService->sendMessage($adminPhone, $confirmMsg);

        // Notifikasi ke pelanggan
        $this->notificationService->notifyCustomerConsultationScheduled($order->load(['service']));

        return response()->json(['status' => 'success', 'action' => 'schedule_finalized']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // REJECT — Tolak pesanan & catat alasan
    // ─────────────────────────────────────────────────────────────────────────
    private function handleRejectReason(string $message, array $session, string $adminPhone, string $sessionKey): \Illuminate\Http\JsonResponse
    {
        $orderNumber = $session['order_number'];
        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            cache()->forget($sessionKey);
            $this->waService->sendMessage($adminPhone, "❌ Pesanan *#{$orderNumber}* tidak ditemukan.");
            return response()->json(['status' => 'error', 'reason' => 'order_not_found']);
        }

        $order->update([
            'status'      => Order::STATUS_CANCELLED,
            'admin_notes' => "Ditolak Admin: {$message}",
        ]);

        cache()->forget($sessionKey);

        $rejectMsg  = "✅ Pesanan *#{$orderNumber}* berhasil *ditolak*.\n";
        $rejectMsg .= "📋 Pelanggan: {$order->name}\n";
        $rejectMsg .= "📝 Alasan: {$message}";

        $this->waService->sendMessage($adminPhone, $rejectMsg);

        return response()->json(['status' => 'success', 'action' => 'order_rejected']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LEGACY — Dukung format lama JADWAL#... (tanpa state machine)
    // ─────────────────────────────────────────────────────────────────────────
    private function handleLegacyJadwal(string $message, string $adminPhone): \Illuminate\Http\JsonResponse
    {
        $parts = explode('#', $message);

        if (count($parts) < 5) {
            $reply  = "*Format Perintah Lama Salah!*\n\n";
            $reply .= "Format: *JADWAL#NoPesanan#YYYY-MM-DD#Waktu#Tempat*\n\n";
            $reply .= "Atau gunakan bot interaktif — pesanan baru akan otomatis dikirim dengan tombol pilihan.";
            $this->waService->sendMessage($adminPhone, $reply);
            return response()->json(['status' => 'error', 'reason' => 'invalid_legacy_format']);
        }

        $orderNumber = trim($parts[1]);
        $dateStr     = trim($parts[2]);
        $timeStr     = trim($parts[3]);
        $placeStr    = trim($parts[4]);

        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            $this->waService->sendMessage($adminPhone, "❌ Pesanan *#{$orderNumber}* tidak ditemukan.");
            return response()->json(['status' => 'error', 'reason' => 'order_not_found']);
        }

        try {
            $consultationDate = Carbon::parse($dateStr);
        } catch (\Exception $e) {
            $this->waService->sendMessage($adminPhone, "❌ Format tanggal *{$dateStr}* salah. Gunakan YYYY-MM-DD.");
            return response()->json(['status' => 'error', 'reason' => 'invalid_date']);
        }

        $order->update([
            'consultation_date'         => $consultationDate,
            'consultation_time'         => $timeStr,
            'consultation_place'        => $placeStr,
            'status'                    => Order::STATUS_SCHEDULED,
            'is_consultation_confirmed' => true,
        ]);

        $adminOrderUrl = route('admin.orders.show', $order);
        $reply  = "✅ *Jadwal Ditetapkan (Legacy):*\n";
        $reply .= "📋 #{$orderNumber} | 👤 {$order->name}\n";
        $reply .= "📅 {$consultationDate->translatedFormat('d F Y')} ⏰ {$timeStr} 📍 {$placeStr}\n\n";
        $reply .= "👉 {$adminOrderUrl}";

        $this->waService->sendMessage($adminPhone, $reply);
        $this->notificationService->notifyCustomerConsultationScheduled($order->load(['service']));

        return response()->json(['status' => 'success', 'action' => 'legacy_schedule_updated']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────
    private function handleUnknownState(string $sessionKey, string $adminPhone): \Illuminate\Http\JsonResponse
    {
        cache()->forget($sessionKey);
        $this->waService->sendMessage($adminPhone, "⚠️ Terjadi kesalahan pada sesi bot. Sesi direset. Silakan tunggu pesanan baru atau hubungi developer.");
        return response()->json(['status' => 'error', 'reason' => 'unknown_state']);
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }
        if (!empty($digits) && !str_starts_with($digits, '62')) {
            $digits = '62' . $digits;
        }
        return $digits;
    }
}
