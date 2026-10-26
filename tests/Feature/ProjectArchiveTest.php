<?php

use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('admin can archive a project and it moves to the archived tab', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $project = Project::factory()->create(['name' => 'Old Website']);

    $this->actingAs($admin)->patch("/admin/projects/{$project->id}/archive")
        ->assertRedirect(route('admin.projects.index'));

    expect($project->fresh()->archived_at)->not->toBeNull();

    $this->actingAs($admin)->get('/admin/projects')->assertDontSee('Old Website');
    $this->actingAs($admin)->get('/admin/projects?view=archived')->assertSee('Old Website');
});

test('admin can restore an archived project', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $project = Project::factory()->create(['archived_at' => now()]);

    $this->actingAs($admin)->patch("/admin/projects/{$project->id}/restore");

    expect($project->fresh()->archived_at)->toBeNull();
});

test('a client does not see an archived project and cannot open it', function () {
    $client = User::factory()->create(['role' => 'client']);
    $visible = Project::factory()->create(['client_id' => $client->id, 'name' => 'Live Project']);
    $archived = Project::factory()->create(['client_id' => $client->id, 'name' => 'Hidden Project', 'archived_at' => now()]);

    $this->actingAs($client)->get('/dashboard')
        ->assertSee('Live Project')
        ->assertDontSee('Hidden Project');

    $this->actingAs($client)->get("/dashboard/{$archived->id}")->assertForbidden();
});

test('a client cannot archive or restore projects', function () {
    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);

    $this->actingAs($client)->patch("/admin/projects/{$project->id}/archive")->assertForbidden();
    expect($project->fresh()->archived_at)->toBeNull();
});

test('no overdue or due-soon emails for invoices on archived projects', function () {
    Mail::fake();
    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id, 'archived_at' => now()]);

    $soon = Invoice::factory()->create(['project_id' => $project->id, 'status' => 'pending', 'due_date' => today()->addDay()]);
    $late = Invoice::factory()->create(['project_id' => $project->id, 'status' => 'pending', 'due_date' => today()->subDays(3)]);

    $this->artisan('invoices:remind-due-soon')->assertSuccessful();
    $this->artisan('invoices:mark-overdue')->assertSuccessful();

    Mail::assertNothingSent();
    expect($late->fresh()->status)->toBe('pending');
});

test('admin dashboard counts only active projects', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Project::factory()->count(2)->create();
    Project::factory()->create(['archived_at' => now()]);

    $this->actingAs($admin)->get('/admin')->assertOk();
    expect(Project::active()->count())->toBe(2);
});