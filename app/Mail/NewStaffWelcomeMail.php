<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewStaffWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $staffName;
    public string $email;
    public string $temporaryPassword;

    public function __construct(string $staffName, string $email, string $temporaryPassword)
    {
        $this->staffName         = $staffName;
        $this->email             = $email;
        $this->temporaryPassword = $temporaryPassword;
    }

    public function build()
    {
        return $this->subject('Welcome to 22UniMart — Account Created')
                    ->view('emails.new-staff-welcome');
    }
}
