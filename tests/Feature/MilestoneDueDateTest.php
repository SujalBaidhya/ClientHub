<?php

use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can add a milestone with a due date', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $project = Project::factory()->create();

    $this->actingAs($admin)->post("/admin/projects/{$project->id}/milestones", [
        'title' => 'Design',
        'status' => 'pending',
        'due_date' => '2026-12-01',
    ])->assertRedirect();

    expect(Milestone::first()->due_date->toDateString())->toBe('2026-12-01');
});

test('admin can change and clear a milestone due date, and bad dates are rejected', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $project = Project::factory()->create();
    $milestone = Milestone::factory()->create(['project_id' => $project->id, 'due_date' => '2026-12-01']);
    $url = "/admin/projects/{$project->id}/milestones/{$milestone->id}";

    $this->actingAs($admin)->put($url, ['title' => 'T', 'status' => 'pending', 'due_date' => '2026-12-15']);
    expect($milestone->fresh()->due_date->toDateString())->toBe('2026-12-15');

    $this->actingAs($admin)->put($url, ['title' => 'T', 'status' => 'pending', 'due_date' => 'not-a-date'])
        ->assertSessionHasErrors('due_date');

    $this->actingAs($admin)->put($url, ['title' => 'T', 'status' => 'pending', 'due_date' => null]);
    expect($milestone->fresh()->due_date)->toBeNull();
});

test('overdue logic ignores completed milestones and missing dates', function () {
    $late = Milestone::factory()->make(['status' => 'pending', 'due_date' => today()->subDays(2)]);
    $done = Milestone::factory()->make(['status' => 'completed', 'due_date' => today()->subDays(2)]);
    $none = Milestone::factory()->make(['status' => 'pending', 'due_date' => null]);
    $today = Milestone::factory()->make(['status' => 'pending', 'due_date' => today()]);

    expect($late->isOverdue())->toBeTrue();
    expect($late->daysUntilDue())->toBe(-2);
    expect($done->isOverdue())->toBeFalse();
    expect($none->isOverdue())->toBeFalse();
    expect($today->isOverdue())->toBeFalse();
});

test('client sees overdue badge and next up on their dashboard', function () {
    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);
    Milestone::factory()->create(['project_id' => $project->id, 'title' => 'Late One', 'status' => 'pending', 'due_date' => today()->subDays(3)]);

    $this->actingAs($client)->get('/dashboard')
        ->assertOk()
        ->assertSee('Overdue by 3 day(s)')
        ->assertSee('Next up:');
});

test('template milestones get dates spread between project start and due date', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);

    $this->actingAs($admin)->post('/admin/projects', [
        'client_id' => $client->id,
        'name' => 'Templated',
        'status' => 'pending',
        'start_date' => '2026-11-01',
        'due_date' => '2026-12-01',
        'template' => 'website',
    ]);

    $dates = Milestone::orderBy('id')->pluck('due_date');
    expect($dates)->toHaveCount(5);
    expect($dates->last()->toDateString())->toBe('2026-12-01');
    expect($dates->first()->gt(Carbon\Carbon::parse('2026-11-01')))->toBeTrue();
});