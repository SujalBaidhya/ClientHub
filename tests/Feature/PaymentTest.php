<?php

use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use App\Mail\PaymentReceivedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a client can view the esewa redirect form for their unpaid invoice', function () {
    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $invoice = Invoice::factory()->create([
        'project_id' => $project->id,
        'status' => 'pending',
        'amount' => 1000.00,
    ]);

    $response = $this->actingAs($client)->get("/invoices/{$invoice->id}/pay");

    $response->assertStatus(200);
    $response->assertViewIs('payment.esewa-redirect');
    $response->assertViewHas('signature');
});

test('a client cannot initiate payment for another client invoice', function () {
    $client = User::factory()->create(['role' => 'client']);
    $otherClient = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $otherClient->id]);
    $invoice = Invoice::factory()->create([
        'project_id' => $project->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($client)->get("/invoices/{$invoice->id}/pay");

    $response->assertStatus(403);
});

test('esewa success callback marks invoice as paid', function () {
    $invoice = Invoice::factory()->create(['status' => 'pending']);
    
    $payload = [
        'status' => 'COMPLETE',
        'transaction_uuid' => "invoice-{$invoice->id}-test1234",
        'total_amount' => $invoice->amount,
    ];

    $encodedData = base64_encode(json_encode($payload));

    $response = $this->get('/payment/success?data=' . $encodedData);

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'status' => 'paid',
    ]);
});
test('an email receipt is sent to the client upon successful payment', function () {
    Mail::fake();

    $client = User::factory()->create(['role' => 'client', 'email' => 'client@example.com']);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $invoice = Invoice::factory()->create([
        'project_id' => $project->id,
        'status' => 'pending',
    ]);

    $payload = [
        'status' => 'COMPLETE',
        'transaction_uuid' => "invoice-{$invoice->id}-test1234",
        'total_amount' => $invoice->amount,
    ];

    $encodedData = base64_encode(json_encode($payload));

    $this->get('/payment/success?data=' . $encodedData);

    Mail::assertSent(PaymentReceivedMail::class, function ($mail) use ($client, $invoice) {
        return $mail->hasTo($client->email) && $mail->invoice->id === $invoice->id;
    });
});