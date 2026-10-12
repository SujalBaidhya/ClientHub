<?php

namespace App\Console\Commands;

use App\Mail\InvoiceOverdueMail;
use App\Models\ActivityLog;
use App\Models\Invoice;
use Illuminate\Console\Command;
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

        foreach ($invoices as $invoice) {
            $invoice->update(['status' => 'overdue']);

            if ($invoice->project?->client) {
                Mail::to($invoice->project->client->email)->send(new InvoiceOverdueMail($invoice));
            }

            ActivityLog::record(
                "Invoice {$invoice->invoice_number} for \"{$invoice->project->name}\" is now overdue",
                route('admin.invoices.create', $invoice->project)
            );
        }

        $this->info("Marked {$invoices->count()} invoice(s) as overdue and sent reminder emails.");
    }
}