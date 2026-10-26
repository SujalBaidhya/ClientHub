<?php

use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('only invoices whose due date has passed become overdue', function () {
    Mail::fake();

    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);

    $dueToday = Invoice::factory()->create([
        'project_id' => $project->id,
        'status' => 'pending',
        'due_date' => today(),
    ]);
    $dueYesterday = Invoice::factory()->create([
        'project_id' => $project->id,
        'status' => 'pending',
        'due_date' => today()->subDay(),
    ]);

    $this->artisan('invoices:mark-overdue')->assertSuccessful();

    expect($dueToday->fresh()->status)->toBe('pending');
    expect($dueYesterday->fresh()->status)->toBe('overdue');

    // the client can see the overdue notice in their activity feed
    expect(
        ActivityLog::where('project_id', $project->id)->where('client_visible', true)->exists()
    )->toBeTrue();
});