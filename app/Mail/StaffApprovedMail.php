<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaffApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $staffName;
    public string $staffEmail;
    public string $loginUrl;

    public function __construct(string $staffName, string $staffEmail)
    {
        $this->staffName  = $staffName;
        $this->staffEmail = $staffEmail;
        $this->loginUrl   = url('/login');
    }

    public function build()
    {
        return $this->subject('✅ Your 22UniMart Staff Account Has Been Approved')
                    ->view('emails.staff-approved');
    }
}
