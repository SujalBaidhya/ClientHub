<?php

use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('a client can mark their own milestone as complete', function () {
    Mail::fake();

    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $milestone = Milestone::factory()->create([
        'project_id' => $project->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($client)
        ->post("/milestones/{$milestone->id}/complete");

    $response->assertRedirect();
    $this->assertDatabaseHas('milestones', [
        'id' => $milestone->id,
        'status' => 'completed',
    ]);
});

test('requesting revision resets status and clears approved_at and completed_at', function () {
    Mail::fake();

    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $milestone = Milestone::factory()->create([
        'project_id' => $project->id,
        'status' => 'completed',
        'completed_at' => now(),
        'approved_at' => now(),
    ]);

    $response = $this->actingAs($client)
        ->post(route('client.milestones.request-revision', $milestone), [
            'client_notes' => 'Needs revision on logo sizing.',
        ]);

    $response->assertRedirect();
    $fresh = $milestone->fresh();
    expect($fresh->status)->toBe('in_progress');
    expect($fresh->approved_at)->toBeNull();
    expect($fresh->completed_at)->toBeNull();
});

test('a client cannot mark someone elses milestone as complete', function () {
    $client = User::factory()->create(['role' => 'client']);
    $otherClient = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $otherClient->id]);
    $milestone = Milestone::factory()->create([
        'project_id' => $project->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($client)
        ->post("/milestones/{$milestone->id}/complete");

    $response->assertStatus(403);
    $this->assertDatabaseHas('milestones', [
        'id' => $milestone->id,
        'status' => 'pending',
    ]);
});
