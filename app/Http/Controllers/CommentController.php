<?php

namespace App\Http\Controllers;

use App\Mail\ClientActivityMail;
use App\Models\Milestone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CommentController extends Controller
{
    public function store(Request $request, Milestone $milestone)
    {
        $user = Auth::user();

        // Security check: only the client who owns this milestone's project,
        // or any admin, is allowed to comment on it.
        $isOwner = $milestone->project->client_id === $user->id;
        $isAdmin = $user->role === 'admin';

        abort_unless($isOwner || $isAdmin, 403);

        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

                $milestone->comments()->create([
            'user_id' => $user->id,
            'body' => $request->body,
        ]);

        // Only notify admins when a client comments — no need to
        // notify anyone when an admin comments on their own project.
        if ($isOwner) {
            $admins = User::where('role', 'admin')->pluck('email');

            if ($admins->isNotEmpty()) {
                Mail::to($admins)->send(
                    new ClientActivityMail($user, $milestone, 'comment', $request->body)
                );
            }
        }

        return back()->with('success', 'Comment added.');
    }
}