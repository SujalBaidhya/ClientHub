<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->client = User::factory()->create([
        'role' => 'client',
    ]);

    $this->project = Project::factory()->create([
        'client_id' => $this->client->id,
    ]);
});

test('guests are redirected to login when accessing admin file routes', function () {
    $this->get(route('admin.files.create', $this->project))
        ->assertRedirect(route('login'));

    $this->post(route('admin.files.store', $this->project), [])
        ->assertRedirect(route('login'));
});

test('clients are forbidden from accessing admin file routes', function () {
    $this->actingAs($this->client)
        ->get(route('admin.files.create', $this->project))
        ->assertForbidden();

    $this->actingAs($this->client)
        ->post(route('admin.files.store', $this->project), [])
        ->assertForbidden();
});

test('admins can access file routes', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.files.create', $this->project))
        ->assertOk();
});

test('only the owning client and admins can view a project file', function () {
    Storage::fake('public');
    Storage::disk('public')->put('files/doc.pdf', '%PDF-1.4 test');

    $file = $this->project->files()->create([
        'uploaded_by' => $this->client->id,
        'original_name' => 'doc.pdf',
        'file_path' => 'files/doc.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 13,
    ]);

    $otherClient = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($otherClient)->get(route('files.view', $file))->assertForbidden();
    $this->actingAs($this->client)->get(route('files.view', $file))->assertOk();
    $this->actingAs($admin)->get(route('files.view', $file))->assertOk();
});
