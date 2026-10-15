<?php

use App\Models\Invoice;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('a client cannot comment on another clients milestone', function () {
    $client = User::factory()->create(['role' => 'client']);
    $other = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $other->id]);
    $milestone = Milestone::factory()->create(['project_id' => $project->id]);

    $this->actingAs($client)
        ->post(route('comments.store', $milestone), ['body' => 'hello'])
        ->assertForbidden();
});

test('an admin can approve any milestone', function () {
    Mail::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $milestone = Milestone::factory()->create(['project_id' => $project->id, 'status' => 'pending']);

    $this->actingAs($admin)
        ->post(route('client.milestones.approve', $milestone))
        ->assertRedirect();

    expect($milestone->fresh()->status)->toBe('completed');
});

test('admin routes reject an invoice that belongs to a different project', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $projectA = Project::factory()->create();
    $projectB = Project::factory()->create();
    $invoice = Invoice::factory()->create(['project_id' => $projectB->id]);

    $this->actingAs($admin)
        ->delete(route('admin.invoices.destroy', [$projectA, $invoice]))
        ->assertNotFound();

    expect(Invoice::find($invoice->id))->not->toBeNull();
});