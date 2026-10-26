<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download(Invoice $invoice)
    {
        abort_unless(
            Auth::user()->role === 'admin' || $invoice->project->client_id === Auth::id(),
            403
        );

        $pdf = Pdf::loadView('invoice-pdf', compact('invoice'));

        return $pdf->download("{$invoice->invoice_number}.pdf");
    }
}
