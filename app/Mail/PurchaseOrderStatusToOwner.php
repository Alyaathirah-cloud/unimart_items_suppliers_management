<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderStatusToOwner extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $poNumber;
    public $status;
    public $supplierName;

    public function __construct(string $poNumber, string $status, string $supplierName)
    {
        $this->poNumber = $poNumber;
        $this->status = $status;
        $this->supplierName = $supplierName;
    }

    public function build()
    {
        return $this->subject("22UniMart Purchase Order {$this->poNumber} {$this->status}")
                    ->view('emails.purchase_order_status');
    }
}
