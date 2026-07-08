<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\OrderUpdate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderUpdateNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public OrderUpdate $update
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📸 Update Lapangan #' . $this->order->order_number . ' - ' . config('app.business_name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order_update',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
