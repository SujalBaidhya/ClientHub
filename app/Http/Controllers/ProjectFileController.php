<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFile; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
class ProjectFileController extends Controller
{
    public function download(ProjectFile $projectFile)
    {
        abort_unless(
            $projectFile->project->client_id === Auth::id(),
            403
        );

        abort_unless(
            Storage::disk('public')->exists($projectFile->file_path),
            404,
            'File not found in storage.'
        );

        return Storage::disk('public')->download(
            $projectFile->file_path,
            $projectFile->original_name
        );
    }
    public function store(Request $request, Project $project)
    {
        abort_unless($project->client_id === Auth::id(), 403);
       
       $request->validate([
    'file' => [
        'required',
        'file',
        'max:20480', // 20MB max
        'mimes:pdf,jpg,jpeg,png,gif,webp,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip',
    ],
], [
    'file.mimes' => 'This file type is not allowed. Allowed: PDF, images, Word, Excel, PowerPoint, TXT, CSV, ZIP.',
]);

        $uploaded = $request->file('file');
        $fileName = basename(trim($uploaded->getClientOriginalName()));

        $duplicateExists = $project->files()
        ->whereRaw('LOWER(original_name) = ?', [
            mb_strtolower($fileName),
        ])
        ->exists();

        if ($duplicateExists) {
            return back()
                ->withErrors([
                    'file_name' => 'A file named"'.$fileName.'" already exists in this project. Please choose a different filename.',
                ])
                ->withInput();
        }

        $path = $uploaded->store('files', 'public');

        $project->files()->create([
            'uploaded_by' => Auth::id(),
            'original_name' => $fileName,
            'file_path' => $path,
            'mime_type' => $uploaded->getMimeType(),
            'file_size' => $uploaded->getSize(),
        ]);

        \App\Models\ActivityLog::record(
            Auth::user()->name . ' uploaded a file to "' . $project->name . '"',
            route('admin.files.create', $project), $project->id,true
        );

        return redirect()->route('dashboard.project', $project)
            ->with('success', 'File uploaded successfully.');
    }
    public function view(ProjectFile $projectFile)
    {
        $user = Auth::user();

        abort_unless(
            $user->role === 'admin' || $projectFile->project->client_id === $user->id,
            403
        );

        $disk = Storage::disk('public');
        abort_unless($disk->exists($projectFile->file_path), 404);

        // Only PDFs can be shown inline; anything else is offered as a download.
        if ($projectFile->mime_type !== 'application/pdf') {
            return $disk->download($projectFile->file_path, $projectFile->original_name);
        }

        return $disk->response(
            $projectFile->file_path,
            $projectFile->original_name,
            [
                'Content-Type' => 'application/pdf',
                'X-Content-Type-Options' => 'nosniff',
            ],
            'inline'
        );
    }
}