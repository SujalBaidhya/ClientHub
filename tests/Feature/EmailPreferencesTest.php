<?php

use App\Mail\InvoiceCreatedMail;
use App\Mail\InvoiceDueSoonMail;
use App\Mail\InvoiceOverdueMail;
use App\Mail\MilestoneCompletedMail;
use App\Models\Invoice;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('everything is on by default', function () {
    $client = User::factory()->create(['role' => 'client'])->fresh();

    expect($client->wantsEmail('invoices'))->toBeTrue();
    expect($client->wantsEmail('reminders'))->toBeTrue();
    expect($client->wantsEmail('milestones'))->toBeTrue();
});

test('a client can save their email preferences', function () {
    $client = User::factory()->create(['role' => 'client']);

    $this->actingAs($client)->put('/account/notifications', ['email_invoices' => '1'])
        ->assertRedirect();

    $client->refresh();
    expect($client->email_invoices)->toBeTrue();
    expect($client->email_reminders)->toBeFalse();
    expect($client->email_milestones)->toBeFalse();
});

test('the preferences card shows for clients and not for admins', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($client)->get('/account')->assertSee('Email Notifications');
    $this->actingAs($admin)->get('/account')->assertDontSee('Email Notifications');
});

test('new invoice email is skipped when the client turned it off', function () {
    Mail::fake();
    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client', 'email_invoices' => false]);
    $project = Project::factory()->create(['client_id' => $client->id]);

    $this->actingAs($admin)->post("/admin/projects/{$project->id}/invoices", [
        'invoice_number' => 'INV-PREF1', 'status' => 'pending', 'amount' => 100,
    ]);

    expect(Invoice::where('invoice_number', 'INV-PREF1')->exists())->toBeTrue();
    Mail::assertNothingSent();
});

test('new invoice email is sent when the client keeps it on', function () {
    Mail::fake();
    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);

    $this->actingAs($admin)->post("/admin/projects/{$project->id}/invoices", [
        'invoice_number' => 'INV-PREF2', 'status' => 'pending', 'amount' => 100,
    ]);

    Mail::assertSent(InvoiceCreatedMail::class);
});

test('reminder emails respect the reminders preference but the overdue status still changes', function () {
    Mail::fake();
    $client = User::factory()->create(['role' => 'client', 'email_reminders' => false]);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $soon = Invoice::factory()->create(['project_id' => $project->id, 'status' => 'pending', 'due_date' => today()->addDay()]);
    $late = Invoice::factory()->create(['project_id' => $project->id, 'status' => 'pending', 'due_date' => today()->subDays(2)]);

    $this->artisan('invoices:remind-due-soon')->assertSuccessful();
    $this->artisan('invoices:mark-overdue')->assertSuccessful();

    Mail::assertNotSent(InvoiceDueSoonMail::class);
    Mail::assertNotSent(InvoiceOverdueMail::class);
    expect($late->fresh()->status)->toBe('overdue');
    expect($soon->fresh()->due_reminder_sent_at)->toBeNull();
});

test('milestone completed email respects the milestones preference', function () {
    Mail::fake();
    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client', 'email_milestones' => false]);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $milestone = Milestone::factory()->create(['project_id' => $project->id, 'status' => 'pending']);

    $this->actingAs($admin)->put("/admin/projects/{$project->id}/milestones/{$milestone->id}", [
        'title' => 'X', 'status' => 'completed',
    ]);

    expect($milestone->fresh()->status)->toBe('completed');
    Mail::assertNotSent(MilestoneCompletedMail::class);
});