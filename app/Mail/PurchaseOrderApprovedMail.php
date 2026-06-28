<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string  $poNumber;
    public string  $recipientType; // 'owner' or 'supplier'
    public ?string $details;

    public function __construct(string $poNumber, string $recipientType, ?string $details = null)
    {
        $this->poNumber       = $poNumber;
        $this->recipientType  = $recipientType;
        $this->details        = $details;
    }

    public function build()
    {
        return $this->subject('✅ Purchase Order Approved — ' . $this->poNumber)
                    ->view('emails.po-approved');
    }
}
