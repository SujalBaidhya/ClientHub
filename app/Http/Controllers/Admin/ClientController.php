<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeClientMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str; // <-- add this
use App\Mail\ClientInvitationMail;

class ClientController extends Controller
{
    public function create()
    {
        $clients = User::where('role', 'client')->latest()->get();

        return view('admin.create-client', compact('clients'));
    }

  public function store(Request $request)
{
    // 1. Validate only name and email (no password required from admin)
    $validated = $request->validate([
        'name'  => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
    ]);

    // 2. Generate a unique 40-character invitation token
    $token = Str::random(40);

    // 3. Save the new client with a dummy temporary hashed password
    $client = User::create([
        'name'               => $validated['name'],
        'email'              => $validated['email'],
        'password'           => Hash::make(Str::random(32)),
        'role'               => 'client',
        'invitation_token'   => $token,
        'invitation_sent_at' => now(),
    ]);

    // 4. Generate the one-time link and queue the invitation email
    $inviteUrl = route('invitations.show', ['token' => $token]);
    Mail::to($client->email)->queue(new ClientInvitationMail($client, $inviteUrl));

   return redirect()->route('admin.clients.create')->with('success', 'Client created and invitation email queued!');
}
        public function destroy(User $client)
    {
        abort_if($client->role !== 'client', 404);

        if ($client->projects()->exists()) {
            return back()->withErrors([
                'client' => "Cannot delete {$client->name} — they still have projects assigned. Delete or reassign their projects first.",
            ]);
        }

        $client->delete();

        return redirect()->route('admin.clients.create')
            ->with('success', 'Client account deleted successfully.');
    }
    }