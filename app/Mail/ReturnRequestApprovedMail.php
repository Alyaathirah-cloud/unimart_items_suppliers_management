<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReturnRequestApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $returnId;
    public string $recipientType; // 'owner' or 'supplier'

    public function __construct(string $returnId, string $recipientType)
    {
        $this->returnId      = $returnId;
        $this->recipientType = $recipientType;
    }

    public function build()
    {
        return $this->subject('✅ Return Request Approved — #' . $this->returnId)
                    ->view('emails.return-approved');
    }
}
