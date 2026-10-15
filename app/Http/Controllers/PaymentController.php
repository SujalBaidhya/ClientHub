<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
        $decoded = base64_decode((string) $request->query('data'), true);
        $data = $decoded ? json_decode($decoded, true) : null;

        if (
            !is_array($data)
            || ($data['status'] ?? null) !== 'COMPLETE'
            || empty($data['transaction_uuid'])
        ) {
            return redirect()->route('dashboard')->with('error', 'Payment could not be verified.');
        }

        // LAYER 1: signature proves the data really came from eSewa
        if (!$this->verifySignature($data)) {
            Log::warning('eSewa payment signature verification failed.', $data);
            return redirect()->route('dashboard')->with('error', 'Payment verification failed.');
        }

        // The response must belong to OUR merchant account
        if (($data['product_code'] ?? null) !== config('services.esewa.merchant_code')) {
            Log::warning('eSewa payment product_code mismatch.', $data);
            return redirect()->route('dashboard')->with('error', 'Payment verification failed.');
        }

        preg_match('/^invoice-(\d+)-/', $data['transaction_uuid'], $matches);
        $invoice = isset($matches[1]) ? Invoice::find($matches[1]) : null;

        if (!$invoice) {
            return redirect()->route('dashboard')->with('error', 'Invoice not found.');
        }

        // Prevent double-processing if this callback fires twice.
        if ($invoice->status === 'paid') {
            return redirect()->route('dashboard')->with('success', 'This invoice has already been paid.');
        }

        // The amount eSewa confirms must match the invoice amount
        $paidAmount = (float) str_replace(',', '', (string) ($data['total_amount'] ?? 0));
        if (abs($paidAmount - (float) $invoice->amount) > 0.01) {
            Log::warning('eSewa payment amount mismatch.', $data);
            return redirect()->route('dashboard')->with('error', 'Payment amount does not match the invoice.');
        }

        // LAYER 2: ask eSewa's own servers if the payment really happened
        if (!$this->verifyWithEsewa($data['transaction_uuid'], $data['total_amount'] ?? $invoice->amount)) {
            Log::warning('eSewa server-side status check failed.', $data);
            return redirect()->route('dashboard')->with('error', 'Payment could not be confirmed with eSewa.');
        }

        // Atomic update: only ONE request can flip the invoice to paid
        $updated = Invoice::where('id', $invoice->id)
            ->where('status', '!=', 'paid')
            ->update([
                'status' => 'paid',
                'transaction_code' => $data['transaction_code'] ?? null,
                'paid_at' => now(),
            ]);

        if ($updated === 0) {
            return redirect()->route('dashboard')->with('success', 'This invoice has already been paid.');
        }

        $invoice->refresh();

        \App\Models\ActivityLog::record(
            (auth()->user()->name ?? 'A client') . ' paid invoice "' . $invoice->invoice_number . '" via eSewa',
            route('admin.payments.index'), $invoice->project_id, true
        );

        // A failed email must not break a payment that already succeeded
        try {
            if ($invoice->project && $invoice->project->client) {
                Mail::to($invoice->project->client->email)->send(new PaymentReceivedMail($invoice));
            }
        } catch (\Throwable $e) {
            Log::error("Receipt email failed for invoice {$invoice->invoice_number}: " . $e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', 'Payment successful! Invoice marked as paid.');
    }

    private function verifySignature(array $data): bool
    {
        $fields = explode(',', $data['signed_field_names'] ?? '');

        if (empty($fields)) {
            return false;
        }

        $message = collect($fields)
            ->map(fn ($field) => "{$field}=" . ($data[$field] ?? ''))
            ->implode(',');

        $expectedSignature = base64_encode(
            hash_hmac('sha256', $message, config('services.esewa.secret_key'), true)
        );

        return hash_equals($expectedSignature, $data['signature'] ?? '');
    }

    private function verifyWithEsewa(string $transactionUuid, $totalAmount): bool
    {
        $response = Http::timeout(15)->get(config('services.esewa.status_url'), [
            'product_code' => config('services.esewa.merchant_code'),
            'total_amount' => $totalAmount,
            'transaction_uuid' => $transactionUuid,
        ]);

        if (!$response->successful()) {
            return false;
        }

        return ($response->json('status') ?? null) === 'COMPLETE';
    }

    public function failure()
    {
        return redirect()->route('dashboard')->with('error', 'Payment was not completed.');
    }
}