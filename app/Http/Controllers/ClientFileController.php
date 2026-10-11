<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientFileController extends Controller
{
    public function store(Request $request, Project $project)
    {
        abort_unless($project->client_id === Auth::id(), 403);

        $request->validate([
            'file' => 'required|file|max:20480', // 20MB max
        ]);

        $uploaded = $request->file('file');
        $path = $uploaded->store('files', 'public');

        $project->files()->create([
            'uploaded_by' => Auth::id(),
            'original_name' => $uploaded->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $uploaded->getClientMimeType(),
            'file_size' => $uploaded->getSize(),
        ]);

        \App\Models\ActivityLog::record(
            Auth::user()->name . ' uploaded a file to "' . $project->name . '"',
            route('admin.files.create', $project)
        );

        return redirect()->route('dashboard.project', $project)
            ->with('success', 'File uploaded successfully.');
    }
}