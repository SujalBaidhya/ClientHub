<?php

use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin sees invoices from all projects with totals', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $a = Project::factory()->create();
    $b = Project::factory()->create();

    Invoice::factory()->create(['project_id' => $a->id, 'invoice_number' => 'INV-AAA', 'status' => 'overdue', 'amount' => 100]);
    Invoice::factory()->create(['project_id' => $b->id, 'invoice_number' => 'INV-BBB', 'status' => 'paid', 'paid_at' => now(), 'amount' => 300]);

    $this->actingAs($admin)->get('/admin/invoices')
        ->assertOk()
        ->assertSee('INV-AAA')
        ->assertSee('INV-BBB')
        ->assertSee('100.00')
        ->assertSee('300.00');
});

test('status filter and search narrow the list', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $project = Project::factory()->create();

    Invoice::factory()->create(['project_id' => $project->id, 'invoice_number' => 'INV-OVER', 'status' => 'overdue']);
    Invoice::factory()->create(['project_id' => $project->id, 'invoice_number' => 'INV-PEND', 'status' => 'pending']);

    $this->actingAs($admin)->get('/admin/invoices?status=overdue')
        ->assertSee('INV-OVER')
        ->assertDontSee('INV-PEND');

    $this->actingAs($admin)->get('/admin/invoices?search=INV-PEND')
        ->assertSee('INV-PEND')
        ->assertDontSee('INV-OVER');
});

test('clients cannot open the admin invoices page or export', function () {
    $client = User::factory()->create(['role' => 'client']);

    $this->actingAs($client)->get('/admin/invoices')->assertForbidden();
    $this->actingAs($client)->get('/admin/invoices/export')->assertForbidden();
});

test('csv export contains the invoices', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $project = Project::factory()->create();
    Invoice::factory()->create(['project_id' => $project->id, 'invoice_number' => 'INV-CSV1', 'status' => 'pending']);

    $response = $this->actingAs($admin)->get('/admin/invoices/export');

    $response->assertOk();
    expect($response->streamedContent())->toContain('INV-CSV1');
});