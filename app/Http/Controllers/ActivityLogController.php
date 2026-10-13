<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $logs = ActivityLog::with('user')->visibleTo($user)->latest()->paginate(30);
        $unreadIds = ActivityLog::visibleTo($user)->unreadFor($user)
            ->whereIn('id', $logs->pluck('id'))->pluck('id')->all();
        ActivityLog::markAllReadFor($user);

        return view('activity-log', compact('logs','unreadIds'));
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