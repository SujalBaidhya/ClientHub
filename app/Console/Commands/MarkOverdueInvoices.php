<?php

namespace App\Console\Commands;

use App\Mail\InvoiceOverdueMail;
use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Support\SafeMail;
use Illuminate\Console\Command;

class MarkOverdueInvoices extends Command
{
    protected $signature = 'invoices:mark-overdue';

    protected $description = 'Mark unpaid invoices as overdue once their due date has passed, and notify the client';

    public function handle()
    {
        // whereDate + today(): an invoice due TODAY is not overdue until tomorrow
       $invoices = Invoice::with('project.client')
    ->whereHas('project', fn ($q) => $q->active())
    ->where('status', 'pending')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->get();

        $emailed = 0;
        $failed = 0;

        foreach ($invoices as $invoice) {
            $invoice->update(['status' => 'overdue']);

            if ($invoice->project?->client) {
                SafeMail::send($invoice->project->client->email, new InvoiceOverdueMail($invoice))
                    ? $emailed++
                    : $failed++;
            }

            // project_id + true makes it appear in the client's activity feed too
            ActivityLog::record(
                "Invoice {$invoice->invoice_number} for \"" . ($invoice->project?->name ?? 'a project') . "\" is now overdue",
                $invoice->project ? route('admin.invoices.create', $invoice->project) : null,
                $invoice->project_id,
                true
            );
        }

        $this->info("Marked {$invoices->count()} invoice(s) as overdue. Emails sent: {$emailed}, failed: {$failed}.");
    }
}