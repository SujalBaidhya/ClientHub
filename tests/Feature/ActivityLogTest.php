<?php

use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('creating a project logs an activity entry', function () {
    Mail::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);

    $response = $this->actingAs($admin)->post('/admin/projects', [
        'client_id' => $client->id,
        'name' => 'Test Project',
        'status' => 'pending',
    ]);

    $response->assertRedirect(route('admin.projects.index'));

    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $admin->id,
    ]);

    expect(ActivityLog::first()->description)->toContain('Test Project');
});

test('inviting a client logs an activity entry', function () {
    Mail::fake();

    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->post('/admin/clients', [
        'name' => 'New Client',
        'email' => 'newclient@example.com',
    ]);

    $response->assertRedirect(route('admin.clients.create'));

    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $admin->id,
    ]);

    expect(ActivityLog::first()->description)->toContain('New Client');
});

test('adding a milestone logs an activity entry', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id, 'name' => 'Website Redesign']);

    $response = $this->actingAs($admin)->post("/admin/projects/{$project->id}/milestones", [
        'title' => 'Design Mockups',
        'status' => 'pending',
    ]);

    $response->assertRedirect(route('admin.milestones.create', $project));

    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $admin->id,
    ]);

    expect(ActivityLog::first()->description)
        ->toContain('Design Mockups')
        ->toContain('Website Redesign');
});

test('a successful esewa payment logs an activity entry', function () {
    \Illuminate\Support\Facades\Http::fake([
        'rc.esewa.com.np/*' => \Illuminate\Support\Facades\Http::response(['status' => 'COMPLETE'], 200),
    ]);
    Mail::fake();

    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $invoice = Invoice::factory()->create([
        'project_id' => $project->id,
        'status' => 'pending',
        'invoice_number' => 'INV-TEST-001',
    ]);

    $signedFieldNames = 'total_amount,transaction_uuid,product_code';
    $transactionUuid = "invoice-{$invoice->id}-test1234";
    $message = "total_amount={$invoice->amount},transaction_uuid={$transactionUuid},product_code=" . config('services.esewa.merchant_code');
    $signature = base64_encode(hash_hmac('sha256', $message, config('services.esewa.secret_key'), true));

    $payload = [
        'status' => 'COMPLETE',
        'transaction_uuid' => $transactionUuid,
        'total_amount' => $invoice->amount,
        'product_code' => config('services.esewa.merchant_code'),
        'signed_field_names' => $signedFieldNames,
        'signature' => $signature,
    ];

    $encodedData = base64_encode(json_encode($payload));

    $this->actingAs($client)->get('/payment/success?data=' . $encodedData);

    $this->assertDatabaseHas('activity_logs', []);

    expect(ActivityLog::first()->description)->toContain('INV-TEST-001');
});

test('clients can mark activity notifications as read without 403', function () {
    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $log = ActivityLog::create([
        'description' => 'Test client notification',
        'project_id' => $project->id,
        'client_visible' => true,
    ]);

    $this->actingAs($client)
        ->postJson(route('client.activity.mark_read'))
        ->assertOk()
        ->assertJson(['status' => 'success']);

    expect(ActivityLog::visibleTo($client)->unreadFor($client)->count())->toBe(0);
});

test('notification bell renders client safe url for client users', function () {
    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);

    ActivityLog::create([
        'description' => 'Admin updated an invoice',
        'url' => route('admin.invoices.create', $project),
        'project_id' => $project->id,
        'client_visible' => true,
    ]);

    $response = $this->actingAs($client)->get(route('dashboard'));
    $response->assertOk();
    $response->assertSee(route('dashboard.project', $project));
    $response->assertDontSee(route('admin.invoices.create', $project));
});