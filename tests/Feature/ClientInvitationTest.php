<?php

namespace Tests\Feature;

use App\Models\User;
use App\Mail\ClientInvitationMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ClientInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_invite_client_and_email_is_queued(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.clients.store'), [
            'name'  => 'Test Partner',
            'email' => 'partner@example.com',
        ]);

        $response->assertRedirect(route('admin.clients.create'));

        $this->assertDatabaseHas('users', [
            'name'  => 'Test Partner',
            'email' => 'partner@example.com',
            'role'  => 'client',
        ]);

        $client = User::where('email', 'partner@example.com')->first();
        $this->assertNotNull($client->invitation_token);

        Mail::assertQueued(ClientInvitationMail::class, function ($mail) use ($client) {
            return $mail->hasTo('partner@example.com');
        });
    }

    public function test_client_can_view_set_password_page_with_valid_token(): void
    {
        $client = User::factory()->create([
            'role'             => 'client',
            'invitation_token' => 'valid-invitation-token',
        ]);

        $response = $this->get(route('invitations.show', ['token' => 'valid-invitation-token']));

        $response->assertOk();
        $response->assertViewIs('auth.set-password');
        $response->assertSee('Set Your Password');
    }

    public function test_client_can_set_password_and_login_successfully(): void
    {
        $client = User::factory()->create([
            'role'             => 'client',
            'invitation_token' => 'token-to-activate',
        ]);

        $response = $this->post(route('invitations.update', ['token' => 'token-to-activate']), [
            'password'              => 'secureSecret123!',
            'password_confirmation' => 'secureSecret123!',
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($client);

        $client->refresh();
        $this->assertNull($client->invitation_token);
        $this->assertTrue(Hash::check('secureSecret123!', $client->password));
    }
}