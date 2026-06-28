<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupplierInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    // Expose individual fields so both email templates can access them
    public string $contactPerson;
    public string $companyName;
    public string $loginUrl;
    public string $contactEmail;
    public string $password;
    public string $ownerContact;
    public string $telegramToken;

    public function __construct(array $data)
    {
        $this->data          = $data;
        $this->contactPerson = $data['contact_person'] ?? 'Supplier';
        $this->companyName   = $data['company_name']   ?? '22UniMart';
        $this->loginUrl      = $data['portal_url']     ?? url('/supplier/login');
        $this->contactEmail  = $data['email']          ?? '';
        $this->password      = $data['password']       ?? '';
        $this->ownerContact  = $data['owner_phone']    ?? 'N/A';
        $this->telegramToken = $data['telegram_token'] ?? '';
    }

    public function build()
    {
        return $this->subject("You're Invited — {$this->companyName} Supplier Portal Access")
                    ->view('emails.supplier_invitation');
    }
}
