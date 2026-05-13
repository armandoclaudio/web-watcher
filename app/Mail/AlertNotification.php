<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AlertNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Collection $records) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New alerts!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.notification',
        );
    }
}
