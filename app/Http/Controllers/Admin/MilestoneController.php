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
use Illuminate\Support\Facades\Auth;

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

       Gate::authorize('respond', $milestone);

        $milestone->update([
            'status' => 'completed',
            'approved_at' => now(),
            'completed_at' => now(),
        ]);

        \App\Models\ActivityLog::record(
            $user->name . ' approved milestone "' . $milestone->title . '" on ' . $project->name,
            route('admin.milestones.create', $project), $project->id, true
        );

        if ($project->client?->email) {
            SafeMail::send($project->client->email, new MilestoneCompletedMail($milestone));
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

       Gate::authorize('respond', $milestone);

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