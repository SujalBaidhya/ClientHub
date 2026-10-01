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
    \Illuminate\Support\Facades\Http::fake([
        'rc.esewa.com.np/*' => \Illuminate\Support\Facades\Http::response(['status' => 'COMPLETE'], 200),
    ]);

    $invoice = Invoice::factory()->create(['status' => 'pending']);

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

    $response = $this->get('/payment/success?data=' . $encodedData);

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'status' => 'paid',
    ]);
});
test('an email receipt is sent to the client upon successful payment', function () {
    \Illuminate\Support\Facades\Http::fake([
        'rc.esewa.com.np/*' => \Illuminate\Support\Facades\Http::response(['status' => 'COMPLETE'], 200),
    ]);
    Mail::fake();

    $client = User::factory()->create(['role' => 'client', 'email' => 'client@example.com']);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $invoice = Invoice::factory()->create([
        'project_id' => $project->id,
        'status' => 'pending',
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

    $this->get('/payment/success?data=' . $encodedData);

    Mail::assertSent(PaymentReceivedMail::class, function ($mail) use ($client, $invoice) {
        return $mail->hasTo($client->email) && $mail->invoice->id === $invoice->id;
    });
});
test('a fake success callback with an invalid signature is rejected', function () {
    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $invoice = Invoice::factory()->create([
        'project_id' => $project->id,
        'status' => 'pending',
    ]);

    // Craft fake "success" data as an attacker would, without knowing
    // the real secret key used to sign genuine eSewa responses.
    $fakeData = [
        'transaction_code' => 'FAKE123',
        'status' => 'COMPLETE',
        'total_amount' => $invoice->amount,
        'transaction_uuid' => 'invoice-' . $invoice->id . '-fake',
        'product_code' => config('services.esewa.merchant_code'),
        'signed_field_names' => 'total_amount,transaction_uuid,product_code',
        'signature' => 'this-is-not-a-real-signature',
    ];

    $encoded = base64_encode(json_encode($fakeData));

    $response = $this->actingAs($client)->get('/payment/success?data=' . $encoded);

    $response->assertRedirect(route('dashboard'));
    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'status' => 'pending',
    ]);
});

test('an already paid invoice cannot be marked paid again by replaying an old success link', function () {
    $client = User::factory()->create(['role' => 'client']);
    $project = Project::factory()->create(['client_id' => $client->id]);
    $invoice = Invoice::factory()->create([
        'project_id' => $project->id,
        'status' => 'paid',
        'transaction_code' => 'REAL123',
    ]);

    $signedFieldNames = 'total_amount,transaction_uuid,product_code';
    $transactionUuid = 'invoice-' . $invoice->id . '-oldone';
    $message = "total_amount={$invoice->amount},transaction_uuid={$transactionUuid},product_code=" . config('services.esewa.merchant_code');
    $signature = base64_encode(hash_hmac('sha256', $message, config('services.esewa.secret_key'), true));

    $data = [
        'transaction_code' => 'REAL123',
        'status' => 'COMPLETE',
        'total_amount' => $invoice->amount,
        'transaction_uuid' => $transactionUuid,
        'product_code' => config('services.esewa.merchant_code'),
        'signed_field_names' => $signedFieldNames,
        'signature' => $signature,
    ];

    $encoded = base64_encode(json_encode($data));

    $response = $this->actingAs($client)->get('/payment/success?data=' . $encoded);

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('success', 'This invoice has already been paid.');
});