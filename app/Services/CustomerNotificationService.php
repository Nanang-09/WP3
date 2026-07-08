<?php

namespace App\Services;

use App\Mail\OrderUpdateNotification;
use App\Models\Order;
use App\Models\OrderUpdate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomerNotificationService
{
    public function notifyOrderUpdate(Order $order, OrderUpdate $update): void
    {
        if (! $update->is_visible_to_customer) {
            return;
        }

        $order->loadMissing(['service', 'user']);

        $this->sendEmail($order, $update);
        $this->sendWhatsApp($order, $update);
    }

    protected function sendEmail(Order $order, OrderUpdate $update): void
    {
        if (blank($order->email)) {
            return;
        }

        try {
            Mail::to($order->email)
                ->send(new OrderUpdateNotification($order, $update));
        } catch (\Throwable $e) {
            Log::warning('Gagal kirim email update lapangan ke pelanggan: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'update_id' => $update->id,
            ]);
        }
    }

    protected function sendWhatsApp(Order $order, OrderUpdate $update): void
    {
        $phone = $this->normalizePhone($order->phone);

        if ($phone === null) {
            return;
        }

        $token = config('services.fonnte.token');

        if (blank($token)) {
            Log::info('WhatsApp update lapangan (simulasi): ' . $this->buildWhatsAppMessage($order, $update), [
                'target' => $phone,
            ]);

            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $this->buildWhatsAppMessage($order, $update),
            ]);

            if (! $response->successful()) {
                Log::warning('Gagal kirim WhatsApp update lapangan: ' . $response->body(), [
                    'order_id' => $order->id,
                    'update_id' => $update->id,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal kirim WhatsApp update lapangan: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'update_id' => $update->id,
            ]);
        }
    }

    protected function buildWhatsAppMessage(Order $order, OrderUpdate $update): string
    {
        $businessName = config('app.business_name');
        $detailUrl = route('order.success', $order);

        return implode("\n", array_filter([
            "Halo {$order->name},",
            '',
            "Ada update lapangan untuk pesanan *#{$order->order_number}* ({$order->service->name}) dari {$businessName}.",
            '',
            "*{$update->title}*",
            "Progres: {$update->progress_percent}%",
            $update->description,
            $update->status_after_update ? 'Status: ' . (new Order(['status' => $update->status_after_update]))->status_label : null,
            '',
            "Lihat detail: {$detailUrl}",
        ]));
    }

    protected function normalizePhone(?string $phone): ?string
    {
        if (blank($phone)) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        if (! str_starts_with($digits, '62')) {
            $digits = '62' . ltrim($digits, '0');
        }

        return $digits;
    }
}
