<?php

namespace App\Mail;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // 1. Ensure this is imported
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceivedMail extends Mailable implements ShouldQueue // 2. Add implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Confirmation: Invoice #' . $this->invoice->invoice_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-received',
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('invoice-pdf', ['invoice' => $this->invoice]);

        return [
            Attachment::fromData(fn () => $pdf->output(), "{$this->invoice->invoice_number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}