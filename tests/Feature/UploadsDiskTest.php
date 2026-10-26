<?php

use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('uploads go to the disk named in UPLOADS_DISK and are removed from it on delete', function () {
    config(['filesystems.uploads_disk' => 'cloud']);
    Storage::fake('cloud');

    $admin = User::factory()->create(['role' => 'admin']);
    $project = Project::factory()->create();

    $this->actingAs($admin)->post("/admin/projects/{$project->id}/files", [
        'file' => UploadedFile::fake()->create('brief.pdf', 50, 'application/pdf'),
    ]);

    $file = ProjectFile::firstOrFail();
    Storage::disk('cloud')->assertExists($file->file_path);

    $this->actingAs($admin)->delete("/admin/projects/{$project->id}/files/{$file->id}");
    Storage::disk('cloud')->assertMissing($file->file_path);
});

test('admin can upload non-pdf files like images and documents', function () {
    Storage::fake('public');

    $admin = User::factory()->create(['role' => 'admin']);
    $project = Project::factory()->create();

    $response = $this->actingAs($admin)->post("/admin/projects/{$project->id}/files", [
        'file' => UploadedFile::fake()->create('mockup.png', 100, 'image/png'),
    ]);

    $response->assertRedirect(route('admin.files.create', $project));
    $this->assertDatabaseHas('project_files', [
        'project_id' => $project->id,
        'original_name' => 'mockup.png',
    ]);
});

test('the default uploads disk is the local public disk', function () {
    expect(config('filesystems.uploads_disk'))->toBe('public');
});
