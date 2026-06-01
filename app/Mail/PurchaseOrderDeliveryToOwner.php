<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderDeliveryToOwner extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $poNumber;
    public $deliveryDate;
    public $deliveryTime;

    public function __construct(string $poNumber, string $deliveryDate, string $deliveryTime)
    {
        $this->poNumber = $poNumber;
        $this->deliveryDate = $deliveryDate;
        $this->deliveryTime = $deliveryTime;
    }

    public function build()
    {
        return $this->subject("22UniMart Delivery Details for {$this->poNumber}")
                    ->view('emails.purchase_order_delivery');
    }
}
