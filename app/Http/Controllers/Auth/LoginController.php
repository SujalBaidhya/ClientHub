<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

       public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $throttleKey = Str::lower($credentials['email']) . '|' . $request->ip();

        // Block after 5 failed attempts, for 60 seconds
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->withErrors([
                    'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
                ])
                ->onlyInput('email');
        }

        /*
         * Check credentials against the users table.
         *
         * Laravel automatically compares the
         * submitted password with the hashed password.
         */
        if (Auth::attempt(
            $credentials,
            $request->boolean('remember')
        )) {
            RateLimiter::clear($throttleKey);

            /*
             * Generate a new session ID after
             * successful authentication.
             *
             * This protects against session fixation.
             */
            $request->session()->regenerate();

            // Login successful
            return redirect()
                ->route('dashboard')
                ->with('success', 'Welcome back to ClientHub.');
        }

        RateLimiter::hit($throttleKey, 60);

        // Login failed
        return back()
            ->withErrors([
                'email' => 'The email or password is incorrect.',
            ])
            ->onlyInput('email');
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        // Remove authenticated user
        Auth::logout();

        /*
         * Completely invalidate the current session.
         */
        $request->session()->invalidate();

        /*
         * Generate a new CSRF token for the next session.
         */
        $request->session()->regenerateToken();

        // Return to login page
        return redirect()
            ->route('login')
            ->with('success', 'You have been logged out.');
    }
}