<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    /**
     * Mengirim pesan WhatsApp.
     * Karena belum ada provider definitif (Fonnte/Wablas), kita catat di Log.
     */
    public function sendMessage(string $phone, string $message): bool
    {
        $apiUrl = 'https://api.fonnte.com/send';
        $apiKey = config('services.fonnte.token');

        if (!$apiKey) {
            Log::info("====================================");
            Log::info("[SIMULASI WHATSAPP] Tujuan: {$phone}");
            Log::info("Pesan:\n{$message}");
            Log::info("====================================");
            return true;
        }

        try {
            // Contoh implementasi untuk Fonnte
            $response = Http::withHeaders([
                'Authorization' => $apiKey
            ])->post($apiUrl, [
                'target' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error("Failed to send WhatsApp message. Response: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Exception when sending WhatsApp message: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengirim pesan WhatsApp dengan tombol quick-reply (Fonnte button API).
     * Maksimal 3 tombol per pesan (limit WhatsApp).
     */
    public function sendButtonMessage(string $phone, string $message, array $buttons): bool
    {
        $apiUrl = 'https://api.fonnte.com/send';
        $apiKey = config('services.fonnte.token');

        // Batasi maksimal 3 tombol
        $buttons = array_slice($buttons, 0, 3);

        if (!$apiKey) {
            Log::info("====================================");
            Log::info("[SIMULASI WA BUTTON] Tujuan: {$phone}");
            Log::info("Pesan:\n{$message}");
            Log::info("Tombol: " . implode(' | ', $buttons));
            Log::info("====================================");
            return true;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey
            ])->post($apiUrl, [
                'target'   => $phone,
                'message'  => $message,
                'type'     => 'button',
                'buttons'  => implode('|', $buttons),
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error("[WA Button] Gagal: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("[WA Button] Exception: " . $e->getMessage());
            return false;
        }
    }
}
