<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReturnRequestCreatedToSupplier extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $returnNumber;
    public $itemName;
    public $reason;

    public function __construct(string $returnNumber, string $itemName, string $reason)
    {
        $this->returnNumber = $returnNumber;
        $this->itemName = $itemName;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject("22UniMart Return Request {$this->returnNumber}")
                    ->view('emails.return_request_created');
    }
}
