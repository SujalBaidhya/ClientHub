<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceCreatedMail;
use App\Models\Invoice;
use App\Models\Project;
use App\Support\SafeMail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
        /**
     * Every invoice across all clients, with status filter, search and totals.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        if (! in_array($status, ['pending', 'paid', 'overdue'], true)) {
            $status = null;
        }

        // Summary cards follow the search but ignore the status filter
        $totals = $this->baseQuery($search)
            ->selectRaw('status, COUNT(*) as total, COALESCE(SUM(amount), 0) as amount')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $invoices = $this->baseQuery($search)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByRaw("CASE status WHEN 'overdue' THEN 0 WHEN 'pending' THEN 1 ELSE 2 END")
            ->orderBy('due_date')
            ->paginate(15)
            ->withQueryString();

        return view('admin.invoices', [
            'invoices' => $invoices,
            'totals' => $totals,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $status = $request->query('status');
        $invoices = $this->baseQuery($request->query('search'))
            ->when(in_array($status, ['pending', 'paid', 'overdue'], true), fn ($q) => $q->where('status', $status))
            ->orderBy('due_date')
            ->get();

        $filename = 'clienthub-invoices-' . now()->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($invoices) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Invoice Number', 'Client', 'Project', 'Amount (NPR)', 'Status', 'Issued', 'Due Date', 'Paid Date']);

            foreach ($invoices as $invoice) {
                fputcsv($file, [
                    $invoice->invoice_number,
                    $invoice->project->client->name ?? 'N/A',
                    $invoice->project->name ?? 'N/A',
                    number_format($invoice->amount, 2, '.', ''),
                    $invoice->status,
                    $invoice->issued_at?->format('Y-m-d'),
                    $invoice->due_date?->format('Y-m-d'),
                    $invoice->paid_at?->format('Y-m-d'),
                ]);
            }

            fclose($file);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function baseQuery(?string $search)
    {
        return Invoice::with('project.client')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('project', function ($p) use ($search) {
                            $p->where('name', 'like', "%{$search}%")
                                ->orWhereHas('client', fn ($c) => $c->where('name', 'like', "%{$search}%"));
                        });
                });
            });
    }
    public function create(Project $project)
    {
        $invoices = $project->invoices()->latest()->get();

        return view('admin.create-invoice', compact('project', 'invoices'));
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|max:255|unique:invoices,invoice_number',
            'status' => 'required|in:pending,paid,overdue',
            'amount' => 'required|numeric|min:0',
            'issued_at' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:issued_at',
        ],
        [
            'due_date.after_or_equal' => 'Due date cannot be earlier than the issued date.',
        ]);

        // An invoice created as "paid" needs a payment date, otherwise it is
        // counted in total revenue but missing from payment history.
        if ($validated['status'] === 'paid') {
            $validated['paid_at'] = now();
        }

        $invoice = $project->invoices()->create($validated);

        \App\Models\ActivityLog::record(
            auth()->user()->name . ' created invoice "' . $invoice->invoice_number . '" for ' . $project->name,
            route('admin.invoices.create', $project), $project->id, true
        );

        if ($project->client?->wantsEmail('invoices')) {
            SafeMail::send($project->client->email, new InvoiceCreatedMail($invoice));
        }

        return redirect()->route('admin.invoices.create', $project)
            ->with('success', 'Invoice added successfully.');
    }

    public function update(Request $request, Project $project, Invoice $invoice)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|max:255|unique:invoices,invoice_number,' . $invoice->id,
            'status' => 'required|in:pending,paid,overdue',
            'amount' => 'required|numeric|min:0',
            'issued_at' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:issued_at',
        ],
        [
            'due_date.after_or_equal' => 'Due date cannot be earlier than the issued date.',
        ]);

        // If an admin manually marks an invoice as paid (not through
        // the real eSewa flow), still record a paid_at date so it
        // shows up correctly in payment history.
        if ($validated['status'] === 'paid' && $invoice->status !== 'paid') {
            $validated['paid_at'] = now();
        }

        $invoice->update($validated);

        \App\Models\ActivityLog::record(
            auth()->user()->name . ' updated invoice "' . $invoice->invoice_number . '" for ' . $project->name,
            route('admin.invoices.create', $project), $project->id, true
        );

        return redirect()->route('admin.invoices.create', $project)
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Project $project, Invoice $invoice)
    {
        $invoice->delete();

        \App\Models\ActivityLog::record(
            auth()->user()->name . ' deleted invoice "' . $invoice->invoice_number . '" from ' . $project->name,
            route('admin.invoices.create', $project), $project->id, true
        );

        return redirect()->route('admin.invoices.create', $project)
            ->with('success', 'Invoice deleted successfully.');
    }
}