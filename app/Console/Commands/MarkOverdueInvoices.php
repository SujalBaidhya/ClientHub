<?php

namespace App\Console\Commands;

use App\Mail\InvoiceOverdueMail;
use App\Models\ActivityLog;
use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MarkOverdueInvoices extends Command
{
    protected $signature = 'invoices:mark-overdue';

    protected $description = 'Mark unpaid invoices as overdue once their due date has passed, and notify the client';

    public function handle()
    {
        $invoices = Invoice::with('project.client')
            ->where('status', 'pending')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->get();

        $emailed = 0;
        $failed = 0;

        foreach ($invoices as $invoice) {
            $invoice->update(['status' => 'overdue']);

            if ($invoice->project?->client) {
                try {
                    Mail::to($invoice->project->client->email)->send(new InvoiceOverdueMail($invoice));
                    $emailed++;
                } catch (\Throwable $e) {
                    $failed++;
                    Log::error("Failed to send overdue email for invoice {$invoice->invoice_number}: " . $e->getMessage());
                }
            }

            ActivityLog::record(
                "Invoice {$invoice->invoice_number} for \"{$invoice->project->name}\" is now overdue",
                route('admin.invoices.create', $invoice->project)
            );
        }

        $this->info("Marked {$invoices->count()} invoice(s) as overdue. Emails sent: {$emailed}, failed: {$failed}.");
    }
}