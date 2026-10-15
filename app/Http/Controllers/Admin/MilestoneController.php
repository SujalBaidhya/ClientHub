<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ClientActivityMail;
use App\Mail\MilestoneCompletedMail;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;
use App\Support\SafeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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

        $milestone = $project->milestones()->create($validated);

        \App\Models\ActivityLog::record(
            auth()->user()->name . ' added milestone "' . $milestone->title . '" to ' . $project->name,
            route('admin.milestones.create', $project), $project->id, true
        );

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

        \App\Models\ActivityLog::record(
            auth()->user()->name . ' updated milestone "' . $milestone->title . '" on ' . $project->name,
            route('admin.milestones.create', $project), $project->id, true
        );

        if ($validated['status'] === 'completed' && !$wasAlreadyCompleted) {
            if ($project->client?->email) {
                SafeMail::send($project->client->email, new MilestoneCompletedMail($milestone));
            }
        }

        return redirect()->route('admin.milestones.create', $project)
            ->with('success', 'Milestone updated successfully.');
    }

    public function destroy(Project $project, Milestone $milestone)
    {
        $title = $milestone->title;

        $milestone->delete();

        \App\Models\ActivityLog::record(
            auth()->user()->name . ' deleted milestone "' . $title . '" from ' . $project->name,
            route('admin.milestones.create', $project), $project->id, true
        );

        return redirect()->route('admin.milestones.create', $project)
            ->with('success', 'Milestone deleted successfully.');
    }

    /**
     * Mark a milestone as approved (by the client, or by an admin on their behalf).
     */
    public function approve(Milestone $milestone)
    {
        Gate::authorize('respond', $milestone);

        $user = auth()->user();
        $project = $milestone->project;
        $isAdmin = ($user->role === 'admin');

        $milestone->update([
            'status' => 'completed',
            'approved_at' => now(),
            'completed_at' => now(),
        ]);

        \App\Models\ActivityLog::record(
            $user->name . ' approved milestone "' . $milestone->title . '" on ' . $project->name,
            route('admin.milestones.create', $project), $project->id, true
        );

        if ($isAdmin) {
            // An admin approved on the client's behalf: tell the client.
            if ($project->client?->email) {
                SafeMail::send($project->client->email, new MilestoneCompletedMail($milestone));
            }
        } else {
            // The client approved: tell the admins.
            $admins = User::where('role', 'admin')->pluck('email');

            if ($admins->isNotEmpty()) {
                SafeMail::send(
                    $admins,
                    new ClientActivityMail($user, $milestone, 'approval', 'Approved this milestone.')
                );
            }
        }

        return back()->with('success', "Milestone '{$milestone->title}' has been approved!");
    }

    /**
     * Submit client feedback and request a revision.
     */
    public function requestRevision(Request $request, Milestone $milestone)
    {
        Gate::authorize('respond', $milestone);

        $user = auth()->user();
        $project = $milestone->project;
        $isAdmin = ($user->role === 'admin');

        $validated = $request->validate([
            'client_notes' => ['required', 'string', 'max:1000'],
        ]);

        $milestone->update([
            'status' => 'in_progress',
            'client_notes' => $validated['client_notes'],
            'approved_at' => null,
        ]);

        \App\Models\ActivityLog::record(
            $user->name . ' requested a revision on "' . $milestone->title . '"',
            route('admin.milestones.create', $project), $project->id, true
        );

        if (!$isAdmin) {
            $admins = User::where('role', 'admin')->pluck('email');

            if ($admins->isNotEmpty()) {
                SafeMail::send(
                    $admins,
                    new ClientActivityMail($user, $milestone, 'revision', $validated['client_notes'])
                );
            }
        }

        return back()->with('success', "Revision requested for '{$milestone->title}'. Feedback submitted.");
    }
}