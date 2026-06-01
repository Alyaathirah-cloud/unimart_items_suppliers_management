<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupplierInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $contactPerson;
    public $contactEmail;
    public $password;
    public $loginUrl;
    public $companyName;
    public $ownerContact;

    public function __construct(
        string $contactPerson,
        string $contactEmail,
        string $password,
        string $loginUrl,
        string $companyName,
        string $ownerContact
    ) {
        $this->contactPerson = $contactPerson;
        $this->contactEmail = $contactEmail;
        $this->password = $password;
        $this->loginUrl = $loginUrl;
        $this->companyName = $companyName;
        $this->ownerContact = $ownerContact;
    }

    public function build()
    {
        return $this->subject('Your 22UniMart Supplier Portal Access')
                    ->view('emails.supplier_invitation');
    }
}
