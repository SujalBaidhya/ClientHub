<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
   private function visibleLogs()
    {
        $user = auth()->user();

         $query = ActivityLog::query();

        if ($user->role !== 'admin') {
            $query->whereIn(
                'project_id',
                $user->projects()->select('projects.id')
            );
        }

        return $query;
    }

    public function index()
    {
        $logs = $this->visibleLogs()
            ->with(['user', 'project'])
            ->latest()
            ->paginate(30);

        $this->visibleLogs()->where('is_read', false)->update(['is_read' => true]);

        return view('admin.activity-log', compact('logs'));
    }
     public function markRead(Request $request)
    {
        $this->visibleLogs()
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success'
            ]);
        }

        return back();
    }
}
