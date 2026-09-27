<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class InvitationController extends Controller
{
    public function show(string $token)
    {
        $user = User::where('invitation_token', $token)->firstOrFail();

        return view('auth.set-password', compact('user', 'token'));
    }

    public function update(Request $request, string $token)
    {
        $user = User::where('invitation_token', $token)->firstOrFail();

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
            'invitation_token' => null,
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Your password has been set. Welcome aboard!');
    }
}