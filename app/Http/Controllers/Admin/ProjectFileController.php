<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectFileController extends Controller
{
    public function create(Request $request, Project $project)
    {
        $uploaders = User::whereIn(
            'id',
            $project->files()
                ->whereNotNull('uploaded_by')
                ->select('uploaded_by')
                ->distinct()
        )
        ->orderBy('name')
        ->get(['id', 'name', 'role']);
        $uploaderId = $request->query('uploader');
        $filesQuery = $project->files()
            ->with('uploadedBy')
            ->latest();
        if ($uploaderId !== null && $uploaderId !== '') {
            $filesQuery->where('uploaded_by', $uploaderId);
        }
        $files = $filesQuery
            ->paginate(10)
            ->withQueryString();

        return view('admin.create-file', compact(
            'project',
            'files',
            'uploaders',
            'uploaderId'
        ));
    }

    public function store(Request $request, Project $project)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:20480', // 20MB max
        ]);

        $uploaded = $request->file('file');
        $path = $uploaded->store('files', 'public');

        $projectFile = $project->files()->create([
            'uploaded_by' => auth()->id(),
            'original_name' => $uploaded->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $uploaded->getClientMimeType(),
            'file_size' => $uploaded->getSize(),
        ]);

        \App\Models\ActivityLog::record(
            auth()->user()->name . ' uploaded file "' . $projectFile->original_name . '" for ' . $project->name,
            route('admin.files.create', $project)
        );

        return redirect()->route('admin.files.create', $project)
            ->with('success', 'File uploaded successfully.');
    }

    public function destroy(Project $project, ProjectFile $file)
    {
        $fileName = $file->original_name;

        \Storage::disk('public')->delete($file->file_path);
        $file->delete();

        \App\Models\ActivityLog::record(
            auth()->user()->name . ' deleted file "' . $fileName . '" from ' . $project->name,
            route('admin.files.create', $project)
        );

        return redirect()->route('admin.files.create', $project)
            ->with('success', 'File deleted successfully.');
    }
}