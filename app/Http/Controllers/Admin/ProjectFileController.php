<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\Request;

class ProjectFileController extends Controller
{
    public function create(Project $project)
    {
        $files = $project->files()->latest()->get();

        return view('admin.create-file', compact('project', 'files'));
    }

    public function store(Request $request, Project $project)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:20480', // 20MB max
        ]);

        $uploaded = $request->file('file');
        $path = $uploaded->store('files', 'public');

        $project->files()->create([
            'uploaded_by' => auth()->id(),
            'original_name' => $uploaded->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $uploaded->getClientMimeType(),
            'file_size' => $uploaded->getSize(),
        ]);

        return redirect()->route('admin.files.create', $project)
            ->with('success', 'File uploaded successfully.');
    }

    public function destroy(Project $project, ProjectFile $file)
    {
        \Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return redirect()->route('admin.files.create', $project)
            ->with('success', 'File deleted successfully.');
    }
}