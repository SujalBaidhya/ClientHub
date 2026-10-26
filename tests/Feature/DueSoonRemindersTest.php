<?php

use App\Mail\InvoiceDueSoonMail;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

function makeInvoiceDue(string $dueDate, string $status = 'pending'): Invoice
{
    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);

    return Invoice::factory()->create([
        'project_id' => $project->id,
        'status' => $status,
        'due_date' => $dueDate,
    ]);
}

test('reminder is sent once for an invoice due within 3 days', function () {
    Mail::fake();
    $invoice = makeInvoiceDue(today()->addDays(2)->toDateString());

    $this->artisan('invoices:remind-due-soon')->assertSuccessful();
    $this->artisan('invoices:remind-due-soon')->assertSuccessful();

    Mail::assertSent(InvoiceDueSoonMail::class, 1);
    expect($invoice->fresh()->due_reminder_sent_at)->not->toBeNull();
});

test('no reminder for far away, past, or already paid invoices', function () {
    Mail::fake();
    makeInvoiceDue(today()->addDays(10)->toDateString());
    makeInvoiceDue(today()->subDay()->toDateString());
    makeInvoiceDue(today()->addDay()->toDateString(), 'paid');

    $this->artisan('invoices:remind-due-soon')->assertSuccessful();

    Mail::assertNothingSent();
});

test('a failed email is not marked as reminded so it retries', function () {
    Mail::shouldReceive('to')->andThrow(new RuntimeException('smtp down'));
    $invoice = makeInvoiceDue(today()->addDay()->toDateString());

    $this->artisan('invoices:remind-due-soon')->assertSuccessful();

    expect($invoice->fresh()->due_reminder_sent_at)->toBeNull();
});

test('reminders do not crash when project client is null', function () {
    Mail::fake();
    $project = Project::factory()->create(['client_id' => null]);
    $invoice = Invoice::factory()->create([
        'project_id' => $project->id,
        'status' => 'pending',
        'due_date' => today()->addDay()->toDateString(),
    ]);

    $this->artisan('invoices:remind-due-soon')->assertSuccessful();

    Mail::assertNothingSent();
    expect($invoice->fresh()->due_reminder_sent_at)->toBeNull();
});
