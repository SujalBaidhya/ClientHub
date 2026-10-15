<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Invoice::with('project.client')
            ->where('status', 'paid')
            ->whereNotNull('paid_at')
            ->when($request->query('search'), function ($query, $search) {
                $query->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('project.client', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest('paid_at')
            ->get();

        $totalReceived = $payments->sum('amount');

        return view('admin.payments', [
            'payments' => $payments,
            'totalReceived' => $totalReceived,
            'search' => $request->query('search'),
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $payments = Invoice::with('project.client')
            ->where('status', 'paid')
            ->whereNotNull('paid_at')
            ->when($request->query('search'), function ($query, $search) {
    $query->where(function ($q) use ($search) {
        $q->where('invoice_number', 'like', "%{$search}%")
            ->orWhereHas('project.client', function ($c) use ($search) {
                $c->where('name', 'like', "%{$search}%");
            });
    });
})
            ->latest('paid_at')
            ->get();

        $filename = 'clienthub-payments-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Invoice Number', 'Client', 'Project', 'Amount (NPR)', 'Paid Date', 'Transaction Code']);

            foreach ($payments as $invoice) {
                fputcsv($file, [
                    $invoice->invoice_number,
                    $invoice->project->client->name ?? 'N/A',
                    $invoice->project->name ?? 'N/A',
                    number_format($invoice->amount, 2, '.', ''),
                    $invoice->paid_at?->format('Y-m-d'),
                    $invoice->transaction_code ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}