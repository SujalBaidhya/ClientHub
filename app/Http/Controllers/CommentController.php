<?php

namespace App\Http\Controllers;

use App\Mail\ClientActivityMail;
use App\Models\ActivityLog;
use App\Models\Milestone;
use App\Models\User;
use App\Support\SafeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Milestone $milestone)
    {
        $user = Auth::user();
        $project = $milestone->project;

        // Check if user is the assigned client or an admin
        $isAdmin = ($user->role === 'admin');
        $isOwner = (
            ($project->client_id && (int) $project->client_id === (int) $user->id) ||
            ($project->user_id && (int) $project->user_id === (int) $user->id) ||
            ($project->client && isset($project->client->user_id) && (int) $project->client->user_id === (int) $user->id) ||
            ($project->client && isset($project->client->email) && $project->client->email === $user->email)
        );

        abort_unless($isOwner || $isAdmin, 403);

        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $comment = $milestone->comments()->create([
            'user_id' => $user->id,
            'body' => $request->body,
        ]);

        // 1. Record in Activity Log so it appears on the Activity / Notifications page
        $projectTitle = $project->title ?? $project->name ?? 'Project';

        ActivityLog::record(
            "{$user->name} commented on milestone '{$milestone->title}' in project '{$projectTitle}'",
            null, $project->id, true
        );

        // 2. Send email notification to admins when a client posts a comment
        if (!$isAdmin) {
            $admins = User::where('role', 'admin')->pluck('email');

            if ($admins->isNotEmpty()) {
                SafeMail::send(
                    $admins,
                    new ClientActivityMail($user, $milestone, 'comment', $request->body)
                );
            }
        }

        return back()->with('success', 'Comment added.');
    }
}