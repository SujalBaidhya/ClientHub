<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceDueSoonMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
    }

    public function build()
    {
        return $this->subject("Invoice due soon: {$this->invoice->invoice_number}")
            ->view('emails.invoice-due-soon');
    }
}