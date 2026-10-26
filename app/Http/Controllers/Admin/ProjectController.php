<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
       public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $view = $request->query('view') === 'archived' ? 'archived' : 'active';

        $base = fn () => Project::when($view === 'archived', fn ($q) => $q->archived(), fn ($q) => $q->active());

        $projects = $base()->with(['client', 'milestones', 'files', 'invoices'])
            ->when($status, fn ($query, $status) => $query->where('status', $status))
            ->when($search, function ($query, $search) {
                // grouped so the search never escapes the active/archived filter
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($c) use ($search) {
                            $c->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $activeCount = Project::active()->count();
        $archivedCount = Project::archived()->count();

        return view('admin.projects', compact('projects', 'search', 'status', 'view', 'activeCount', 'archivedCount'));
    }

    public function create()
    {
        $clients = User::where('role', 'client')->get();
        $templates = config('project_templates');

        return view('admin.create-project', compact('clients', 'templates'));
    }

    public function store(Request $request)
   {
    $validated = $request->validate([
        'client_id' => 'required|exists:users,id',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|in:pending,in_progress,completed',
        'start_date' => 'nullable|date',
        'due_date' => 'nullable|date|after_or_equal:start_date',
        'template' => 'nullable|string|in:' . implode(',', array_keys(config('project_templates'))),
        ], 
        [
            'due_date.after_or_equal' => 'Due date cannot be earlier than the start date.',
    ]);

    $template = $validated['template'] ?? null;
    unset($validated['template']);

    $project = Project::create($validated);

    if ($template && $milestones = config("project_templates.{$template}.milestones")) {
               // With a start and due date on the project, spread the milestone dates evenly between them
        $count = count($milestones);
        $start = !empty($validated['start_date']) ? \Illuminate\Support\Carbon::parse($validated['start_date']) : null;
        $end = !empty($validated['due_date']) ? \Illuminate\Support\Carbon::parse($validated['due_date']) : null;
        $spread = $start && $end && $end->gte($start);

        foreach (array_values($milestones) as $i => $milestone) {
            Milestone::create([
                'due_date' => $spread
                    ? $start->copy()->addDays((int) round($start->diffInDays($end) * ($i + 1) / $count))
                    : null,
                'project_id' => $project->id,
                'title' => $milestone['title'],
                'description' => $milestone['description'] ?? null,
                'status' => 'pending',
            ]);
        }

        \App\Models\ActivityLog::record(
            auth()->user()->name . ' created project "' . $project->name . '" from the "' . config("project_templates.{$template}.label") . '" template',
            route('admin.projects.edit', $project),
            $project->id,
            true
        );
    } else {
        \App\Models\ActivityLog::record(
            auth()->user()->name . ' created project "' . $project->name . '"',
            route('admin.projects.edit', $project),
            $project->id,
            true
        );
    }

  return redirect()->route('admin.projects.index')
    ->with('success', 'Project created successfully.');
}
    public function edit(Project $project)
    {
        $clients = User::where('role', 'client')->get();

        return view('admin.edit-project', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            ], 
            [
                'due_date.after_or_equal' => 'Due date cannot be earlier than the start date.',
        ]);

        $project->update($validated);

        \App\Models\ActivityLog::record(
            auth()->user()->name . ' updated project "' . $project->name . '"',
            route('admin.projects.edit', $project),
            $project->id,
            true
        );

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        // Logged before deleting: activity_logs.project_id is a foreign key, so it
        // can't point at a project row that no longer exists. Admin-only entry.
        \App\Models\ActivityLog::record(
            auth()->user()->name . ' deleted project "' . $project->name . '"',
            route('admin.projects.index')
        );

        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project deleted successfully.');
    }
        public function archive(Project $project)
    {
        $project->update(['archived_at' => now()]);

        // Admin-only entry: clients simply stop seeing the project
        \App\Models\ActivityLog::record(
            auth()->user()->name . ' archived project "' . $project->name . '"',
            route('admin.projects.index', ['view' => 'archived']),
            $project->id,
            false
        );

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project archived. You can restore it from the Archived tab.');
    }

    public function restore(Project $project)
    {
        $project->update(['archived_at' => null]);

        \App\Models\ActivityLog::record(
            auth()->user()->name . ' restored project "' . $project->name . '"',
            route('admin.projects.edit', $project),
            $project->id,
            false
        );

        return redirect()->route('admin.projects.index', ['view' => 'archived'])
            ->with('success', 'Project restored.');
    }
}