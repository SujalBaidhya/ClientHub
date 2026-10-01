<?php

use App\Mail\ClientActivityMail;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('admin is notified by email when a client posts a comment', function () {
    Mail::fake();

    $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);
    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $milestone = Milestone::factory()->create(['project_id' => $project->id]);

    $this->actingAs($client)
        ->post("/milestones/{$milestone->id}/comments", ['body' => 'Looking good so far!']);

    Mail::assertSent(ClientActivityMail::class, function ($mail) use ($admin) {
        return $mail->hasTo($admin->email) && $mail->activityType === 'comment';
    });
});

test('admin is not notified when they comment on their own project', function () {
    Mail::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $milestone = Milestone::factory()->create();

    $this->actingAs($admin)
        ->post("/milestones/{$milestone->id}/comments", ['body' => 'Internal note']);

    Mail::assertNotSent(ClientActivityMail::class);
});

test('admin is notified by email when a client requests a milestone revision', function () {
    Mail::fake();

    $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.com']);
    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $milestone = Milestone::factory()->create(['project_id' => $project->id]);

    $this->actingAs($client)
        ->post("/milestones/{$milestone->id}/request-revision", ['client_notes' => 'Please fix the spacing.']);

    Mail::assertSent(ClientActivityMail::class, function ($mail) use ($admin) {
        return $mail->hasTo($admin->email) && $mail->activityType === 'revision';
    });
});