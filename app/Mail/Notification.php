<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Notification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $subject = 'New alerts!';
    public $records;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($records)
    {
        $this->records = $records;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.notification');
    }
}
