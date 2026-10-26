<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(?Project $project = null)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Fetch all projects belonging to this user/client
        $allProjects = $user->projects()->active()->latest()->get() ?? collect();

        if ($project && $project->exists) {
            // Check that the requested project is in the user's projects list
            $hasAccess = $allProjects->contains('id', $project->id);
            abort_unless($hasAccess, 403);
        } else {
            // Default to the first project
            $project = $allProjects->first();
        }

        if ($project) {
            // Load relations specifically for this project
            $project->load([
                'milestones.comments.user',
                'files.uploadedBy',
                'invoices',
                'client',
            ]);
        }

        return view('dashboard', [
            'project' => $project,
            'allProjects' => $allProjects,
        ]);
    }
}
