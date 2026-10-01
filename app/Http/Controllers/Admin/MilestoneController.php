<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ClientActivityMail;
use App\Mail\MilestoneCompletedMail;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
class MilestoneController extends Controller
{
    public function create(Project $project)
    {
        $milestones = $project->milestones()->latest()->get();

        return view('admin.create-milestone', compact('project', 'milestones'));
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $validated['completed_at'] = $validated['status'] === 'completed'
            ? now()
            : null;

        $project->milestones()->create($validated);

        return redirect()->route('admin.milestones.create', $project)
            ->with('success', 'Milestone added successfully.');
    }

    public function update(Request $request, Project $project, Milestone $milestone)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $wasAlreadyCompleted = $milestone->status === 'completed';

        $validated['completed_at'] = $validated['status'] === 'completed'
            ? ($milestone->completed_at ?? now())
            : null;

        $milestone->update($validated);

        if ($validated['status'] === 'completed' && !$wasAlreadyCompleted) {
            if ($project->client?->email) {
                Mail::to($project->client->email)
                    ->send(new MilestoneCompletedMail($milestone));
            }
        }

        return redirect()->route('admin.milestones.create', $project)
            ->with('success', 'Milestone updated successfully.');
    }

    public function destroy(Project $project, Milestone $milestone)
    {
        $milestone->delete();

        return redirect()->route('admin.milestones.create', $project)
            ->with('success', 'Milestone deleted successfully.');
    }

    /**
     * Mark a milestone as approved by the client.
     */
    public function approve(Milestone $milestone)
    {
        $user = auth()->user();
        $project = $milestone->project;

        $isClientOwner = (
            ($project->client_id && (int) $project->client_id === (int) $user->id) ||
            ($project->user_id && (int) $project->user_id === (int) $user->id) ||
            ($project->client && isset($project->client->user_id) && (int) $project->client->user_id === (int) $user->id) ||
            ($project->client && isset($project->client->email) && $project->client->email === $user->email)
        );

        $isAdmin = ($user->role === 'admin');

        abort_unless($isClientOwner || $isAdmin, 403);

        $milestone->update([
            'status' => 'completed',
            'approved_at' => now(),
            'completed_at' => now(),
        ]);

        if ($project->client?->email) {
            Mail::to($project->client->email)
                ->send(new MilestoneCompletedMail($milestone));
        }

        return back()->with('success', "Milestone '{$milestone->title}' has been approved!");
    }

    /**
     * Submit client feedback and request a revision.
     */
    public function requestRevision(Request $request, Milestone $milestone)
    {
        $user = auth()->user();
        $project = $milestone->project;

        $isClientOwner = (
            ($project->client_id && (int) $project->client_id === (int) $user->id) ||
            ($project->user_id && (int) $project->user_id === (int) $user->id) ||
            ($project->client && isset($project->client->user_id) && (int) $project->client->user_id === (int) $user->id) ||
            ($project->client && isset($project->client->email) && $project->client->email === $user->email)
        );

        $isAdmin = ($user->role === 'admin');

        abort_unless($isClientOwner || $isAdmin, 403);

        $validated = $request->validate([
            'client_notes' => ['required', 'string', 'max:1000'],
        ]);

             $milestone->update([
            'status' => 'in_progress',
            'client_notes' => $validated['client_notes'],
            'approved_at' => null,
        ]);

        // Only notify admins when the client (not an admin) requests
        // the revision — avoids admins emailing themselves.
        if (!$isAdmin) {
            $admins = User::where('role', 'admin')->pluck('email');

            if ($admins->isNotEmpty()) {
                Mail::to($admins)->send(
                    new ClientActivityMail($user, $milestone, 'revision', $validated['client_notes'])
                );
            }
        }

        return back()->with('success', "Revision requested for '{$milestone->title}'. Feedback submitted.");
         }
}