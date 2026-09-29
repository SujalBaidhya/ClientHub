<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Mail\PaymentReceivedMail;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function pay(Invoice $invoice)
    {
        abort_unless($invoice->project->client_id === Auth::id(), 403);
        abort_if($invoice->status === 'paid', 400, 'This invoice is already paid.');

        $transactionUuid = 'invoice-' . $invoice->id . '-' . Str::random(8);

        $amount = number_format($invoice->amount, 2, '.', '');

        $signedFieldNames = 'total_amount,transaction_uuid,product_code';
        $message = "total_amount={$amount},transaction_uuid={$transactionUuid},product_code=" . config('services.esewa.merchant_code');
        $signature = base64_encode(hash_hmac('sha256', $message, config('services.esewa.secret_key'), true));

        return view('payment.esewa-redirect', [
            'invoice' => $invoice,
            'amount' => $amount,
            'transactionUuid' => $transactionUuid,
            'signature' => $signature,
            'signedFieldNames' => $signedFieldNames,
        ]);
    }

    public function success(Request $request)
    {
        $data = json_decode(base64_decode($request->query('data')), true);

        if (!$data || ($data['status'] ?? null) !== 'COMPLETE') {
            return redirect()->route('dashboard')->with('error', 'Payment could not be verified.');
        }

        preg_match('/^invoice-(\d+)-/', $data['transaction_uuid'], $matches);
        $invoiceId = $matches[1] ?? null;

        $invoice = Invoice::find($invoiceId);

        if (!$invoice) {
            return redirect()->route('dashboard')->with('error', 'Invoice not found.');
        }

        $invoice->update([
            'status' => 'paid',
            'transaction_code' => $data['transaction_code'] ?? null,
            'paid_at' => now(),
        ]);

        if ($invoice->project && $invoice->project->client) {
            Mail::to($invoice->project->client->email)->send(new PaymentReceivedMail($invoice));
        }

        return redirect()->route('dashboard')->with('success', 'Payment successful! Invoice marked as paid.');
    }

    public function failure()
    {
        return redirect()->route('dashboard')->with('error', 'Payment was not completed.');
    }
}