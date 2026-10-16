<?php

namespace App\Console\Commands;

use App\Mail\InvoiceDueSoonMail;
use App\Models\Invoice;
use App\Support\SafeMail;
use Illuminate\Console\Command;

class SendDueSoonInvoiceReminders extends Command
{
    protected $signature = 'invoices:remind-due-soon {--days=3 : How many days ahead to remind}';

    protected $description = 'Email clients whose pending invoice is due within the next few days (once per invoice)';

    public function handle()
    {
        $days = max(1, (int) $this->option('days'));

        $invoices = Invoice::with('project.client')
            ->whereHas('project', fn ($q) => $q->active())
            ->where('status', 'pending')
            ->whereNull('due_reminder_sent_at')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '>=', today())
            ->whereDate('due_date', '<=', today()->addDays($days))
            ->get();

        $sent = 0;
        $failed = 0;

        foreach ($invoices as $invoice) {
            $client = $invoice->project?->client;


            // Client opted out of reminders (not marked as sent, so turning it back on still works)
            if (! $client->wantsEmail('reminders')) {
                continue;
            }

            if (SafeMail::send($client->email, new InvoiceDueSoonMail($invoice))) {
                // Only mark as reminded when the email really went out, so a failure is retried next run
                $invoice->forceFill(['due_reminder_sent_at' => now()])->save();
                $sent++;
            } else {
                $failed++;
            }
        }

        $this->info("Due-soon reminders sent: {$sent}, failed: {$failed}.");
    }
}