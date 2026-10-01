<?php

use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('manually marking an invoice as paid also records a paid_at date', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $project = Project::factory()->create();
    $invoice = Invoice::factory()->create([
        'project_id' => $project->id,
        'status' => 'pending',
    ]);

    $this->actingAs($admin)->put("/admin/projects/{$project->id}/invoices/{$invoice->id}", [
        'invoice_number' => $invoice->invoice_number,
        'status' => 'paid',
        'amount' => $invoice->amount,
    ]);

    $invoice->refresh();

    expect($invoice->status)->toBe('paid');
    expect($invoice->paid_at)->not->toBeNull();
});

test('admin payment history page shows paid invoices with correct total', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $project = Project::factory()->create();

    Invoice::factory()->create(['project_id' => $project->id, 'status' => 'paid', 'paid_at' => now(), 'amount' => 100]);
    Invoice::factory()->create(['project_id' => $project->id, 'status' => 'paid', 'paid_at' => now(), 'amount' => 250]);
    Invoice::factory()->create(['project_id' => $project->id, 'status' => 'pending', 'amount' => 500]);

    $response = $this->actingAs($admin)->get('/admin/payments');

    $response->assertOk();
    $response->assertSee('350.00');
});