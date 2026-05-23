<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendETicket extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Transaction $transaction)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'E-Ticket Anda - ' . $this->transaction->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mails.eticket',
            with: [
                'transaction' => $this->transaction,
                'event' => $this->transaction->event,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
