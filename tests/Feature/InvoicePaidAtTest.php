<?php

use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('creating an invoice as paid records a payment date', function () {
    Mail::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);

    $this->actingAs($admin)
        ->post(route('admin.invoices.store', $project), [
            'invoice_number' => 'INV-TEST-PAID',
            'status' => 'paid',
            'amount' => 500,
        ])
        ->assertRedirect();

    expect(Invoice::where('invoice_number', 'INV-TEST-PAID')->first()->paid_at)->not->toBeNull();
});

test('updating an invoice from paid to pending clears paid_at date', function () {
    Mail::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $invoice = Invoice::factory()->create([
        'project_id' => $project->id,
        'invoice_number' => 'INV-REVERT',
        'status' => 'paid',
        'amount' => 500,
        'paid_at' => now(),
    ]);

    $this->actingAs($admin)
        ->put(route('admin.invoices.update', [$project, $invoice]), [
            'invoice_number' => 'INV-REVERT',
            'status' => 'pending',
            'amount' => 500,
        ])
        ->assertRedirect();

    expect($invoice->fresh()->paid_at)->toBeNull();
});

test('an invoice can be created for a project that has no client', function () {
    Mail::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $project = Project::factory()->create(['client_id' => null]);

    $this->actingAs($admin)
        ->post(route('admin.invoices.store', $project), [
            'invoice_number' => 'INV-NO-CLIENT',
            'status' => 'pending',
            'amount' => 100,
        ])
        ->assertRedirect();

    expect(Invoice::where('invoice_number', 'INV-NO-CLIENT')->exists())->toBeTrue();
});
