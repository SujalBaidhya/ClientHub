<?php

use App\Mail\ClientActivityMail;
use App\Mail\MilestoneCompletedMail;
use App\Models\ActivityLog;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();

    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->client = User::factory()->create(['role' => 'client']);
    $this->project = Project::factory()->create(['client_id' => $this->client->id]);
    $this->milestone = Milestone::factory()->create([
        'project_id' => $this->project->id,
        'status' => 'pending',
    ]);
});

test('when a client approves a milestone the admins are emailed, not the client', function () {
    $this->actingAs($this->client)
        ->post(route('client.milestones.approve', $this->milestone))
        ->assertRedirect();

    Mail::assertSent(ClientActivityMail::class, fn ($mail) =>
        $mail->hasTo($this->admin->email) && $mail->activityType === 'approval'
    );
    Mail::assertNotSent(MilestoneCompletedMail::class);
});

test('when an admin approves a milestone the client is emailed', function () {
    $this->actingAs($this->admin)
        ->post(route('client.milestones.approve', $this->milestone))
        ->assertRedirect();

    Mail::assertSent(MilestoneCompletedMail::class, fn ($mail) =>
        $mail->hasTo($this->client->email)
    );
});

test('updating and deleting a milestone are written to the activity log', function () {
    $this->actingAs($this->admin)
        ->put(route('admin.milestones.update', [$this->project, $this->milestone]), [
            'title' => 'Renamed milestone',
            'status' => 'in_progress',
        ])
        ->assertRedirect();

    expect(
        ActivityLog::where('description', 'like', '%updated milestone "Renamed milestone"%')->exists()
    )->toBeTrue();

    $this->actingAs($this->admin)
        ->delete(route('admin.milestones.destroy', [$this->project, $this->milestone]))
        ->assertRedirect();

    expect(
        ActivityLog::where('description', 'like', '%deleted milestone "Renamed milestone"%')->exists()
    )->toBeTrue();
});