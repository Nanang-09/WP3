<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerOrderReceivedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🛠️ Pesanan Anda #' . $this->order->order_number . ' Telah Diterima - ' . config('app.business_name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.customer_received',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
