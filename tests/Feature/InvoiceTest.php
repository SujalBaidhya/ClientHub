<?php

use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a client can download their own invoice', function () {
    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $invoice = Invoice::factory()->create([
        'project_id' => $project->id,
        'invoice_number' => 'INV-TEST-001',
    ]);

    $response = $this->actingAs($client)
        ->get("/invoices/{$invoice->id}/download");

    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
});

test('an admin can download any invoice', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $invoice = Invoice::factory()->create([
        'project_id' => $project->id,
        'invoice_number' => 'INV-TEST-ADMIN',
    ]);

    $response = $this->actingAs($admin)
        ->get("/invoices/{$invoice->id}/download");

    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
});

test('a client cannot download someone elses invoice', function () {
    $client = User::factory()->create(['role' => 'client']);
    $otherClient = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $otherClient->id]);
    $invoice = Invoice::factory()->create(['project_id' => $project->id]);

    $response = $this->actingAs($client)
        ->get("/invoices/{$invoice->id}/download");

    $response->assertStatus(403);
});

test('a guest cannot download any invoice', function () {
    $project = Project::factory()->create();
    $invoice = Invoice::factory()->create(['project_id' => $project->id]);

    $response = $this->get("/invoices/{$invoice->id}/download");

    $response->assertRedirect('/login');
});
