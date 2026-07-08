<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', ActivityLog::class);

        $query = ActivityLog::with('user')->latest();

        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }
        if ($search = $request->get('search')) {
            $query->where('description', 'like', "%{$search}%");
        }

        $logs = $query->paginate(20)->withQueryString();
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('admin.activity-logs.index', compact('logs', 'actions'));
    }
}
