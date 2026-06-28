<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public string  $invoiceNumber;
    public string  $supplierName;
    public ?string $pdfPath; // Absolute local path to PDF file, if available

    public function __construct(string $invoiceNumber, string $supplierName, ?string $pdfPath = null)
    {
        $this->invoiceNumber = $invoiceNumber;
        $this->supplierName  = $supplierName;
        $this->pdfPath       = $pdfPath;
    }

    public function build()
    {
        $mail = $this->subject('Invoice #' . $this->invoiceNumber . ' — 22UniMart')
                     ->view('emails.invoice');

        // Attach the PDF if a valid file path was provided
        if ($this->pdfPath && file_exists($this->pdfPath)) {
            $mail->attach($this->pdfPath, [
                'as'   => 'Invoice-' . $this->invoiceNumber . '.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}
