<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ClientInvitationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function create()
    {
        $clients = User::where('role', 'client')->latest()->get();

        return view('admin.create-client', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        ]);

        $token = Str::random(40);

        $client = User::create([
            'name'               => $validated['name'],
            'email'              => $validated['email'],
            'password'           => Hash::make(Str::random(32)),
            'role'               => 'client',
            'invitation_token'   => $token,
            'invitation_sent_at' => now(),
        ]);

        $inviteUrl = route('invitations.show', ['token' => $token]);
       Mail::to($client->email)->send(new ClientInvitationMail($client, $inviteUrl));

        return redirect()->route('admin.clients.create')->with('success', 'Client created and invitation email queued!');
    }

    public function resendInvite(User $client)
    {
        abort_if($client->role !== 'client', 404);

        if (! $client->invitation_token) {
            return back()->withErrors([
                'client' => 'This client has already activated their account.',
            ]);
        }

        $token = Str::random(40);

        $client->update([
            'invitation_token'   => $token,
            'invitation_sent_at' => now(),
        ]);

        $inviteUrl = route('invitations.show', ['token' => $token]);
       Mail::to($client->email)->send(new ClientInvitationMail($client, $inviteUrl));

        return redirect()->route('admin.clients.create')
            ->with('success', "Invitation re-sent successfully to {$client->email}!");
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