<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowStockMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $productName;
    public int $currentQty;

    public function __construct(string $productName, int $currentQty)
    {
        $this->productName = $productName;
        $this->currentQty  = $currentQty;
    }

    public function build()
    {
        return $this->subject('⚠️ Low Stock Alert — ' . $this->productName)
                    ->view('emails.low-stock');
    }
}
