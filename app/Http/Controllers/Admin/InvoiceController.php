<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceCreatedMail;
use App\Models\Invoice;
use App\Models\Project;
use App\Support\SafeMail;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
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

        SafeMail::send($project->client?->email, new InvoiceCreatedMail($invoice));

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