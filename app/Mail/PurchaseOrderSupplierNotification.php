<?php

namespace App\Mail;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderSupplierNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $purchaseOrder;
    public $action;

    /**
     * Create a new message instance.
     *
     * @param PurchaseOrder $purchaseOrder
     * @param string $action 'created' or 'updated'
     */
    public function __construct(PurchaseOrder $purchaseOrder, string $action = 'created')
    {
        $this->purchaseOrder = $purchaseOrder;
        $this->action = $action;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subjectText = $this->action === 'updated' ? 'Purchase Order Updated' : 'New Purchase Order';
        
        return $this->subject("{$subjectText} - {$this->purchaseOrder->po_number}")
                    ->view('emails.purchase_order_notification');
    }
}

