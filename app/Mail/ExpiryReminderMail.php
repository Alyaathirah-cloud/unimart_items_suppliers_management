<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExpiryReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $productName;
    public string $expiryDate;

    public function __construct(string $productName, string $expiryDate)
    {
        $this->productName = $productName;
        $this->expiryDate  = $expiryDate;
    }

    public function build()
    {
        return $this->subject('⏰ Expiry Reminder — ' . $this->productName)
                    ->view('emails.expiry-reminder');
    }
}
