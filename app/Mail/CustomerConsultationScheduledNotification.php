<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerConsultationScheduledNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📅 Jadwal Konsultasi & Survei Pesanan #' . $this->order->order_number . ' Telah Ditentukan',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.customer_scheduled',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
