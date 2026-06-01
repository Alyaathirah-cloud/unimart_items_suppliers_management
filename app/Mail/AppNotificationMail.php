<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $messageText;

    public function __construct(string $messageText)
    {
        $this->messageText = $messageText;
    }

    public function build()
    {
        return $this->subject('22UniMart Notification')
                    ->view('emails.notification')
                    ->with(['messageText' => $this->messageText]);
    }
}
