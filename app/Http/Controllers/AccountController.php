<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function edit()
    {
        return view('account');
    }
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        $user->forceFill([
            'email_invoices' => $request->boolean('email_invoices'),
            'email_reminders' => $request->boolean('email_reminders'),
            'email_milestones' => $request->boolean('email_milestones'),
        ])->save();

        return back()->with('success', 'Email preferences saved.');
    }
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => 'nullable|confirmed|min:8',
        ]);

        $user->name = $validated['name'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Account updated successfully.');
    }
}