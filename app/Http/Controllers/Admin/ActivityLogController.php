<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $logs = ActivityLog::with('user')->visibleTo($user)->latest()->paginate(30);
        ActivityLog::markAllReadFor($user);
        return view('admin.activity-log', compact('logs'));
    }

    public function markRead(Request $request)
    {
        ActivityLog::markAllReadFor($request->user());

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success']);
        }

        return back();
    }
}