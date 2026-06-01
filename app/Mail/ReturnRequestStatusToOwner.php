<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReturnRequestStatusToOwner extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $returnNumber;
    public $status;

    public function __construct(string $returnNumber, string $status)
    {
        $this->returnNumber = $returnNumber;
        $this->status = $status;
    }

    public function build()
    {
        return $this->subject("22UniMart Return Request {$this->returnNumber} {$this->status}")
                    ->view('emails.return_request_status');
    }
}
