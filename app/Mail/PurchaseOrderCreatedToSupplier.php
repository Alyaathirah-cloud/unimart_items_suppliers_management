<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderCreatedToSupplier extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $poNumber;
    public $itemName;
    public $quantity;
    public $loginUrl;

    public function __construct(string $poNumber, string $itemName, int $quantity, string $loginUrl)
    {
        $this->poNumber = $poNumber;
        $this->itemName = $itemName;
        $this->quantity = $quantity;
        $this->loginUrl = $loginUrl;
    }

    public function build()
    {
        return $this->subject("22UniMart Purchase Order {$this->poNumber}")
                    ->view('emails.purchase_order_created');
    }
}
