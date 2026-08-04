<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Display paginated audit trail activity logs with search & action filtering.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $action = $request->query('action');

        $query = ActivityLog::with('user');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        if (! empty($action)) {
            $query->where('action', $action);
        }

        $logs = $query->orderBy('id', 'asc')->paginate(15)->withQueryString();

        $actions = ActivityLog::distinct('action')->pluck('action');

        return view('admin.activity-logs.index', compact('logs', 'search', 'action', 'actions'));
    }

    /**
     * Delete selected activity log records.
     */
    public function destroySelected(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:activity_logs,id'],
        ]);

        $count = ActivityLog::whereIn('id', $request->input('ids'))->delete();

        return redirect()->route('admin.activity-logs.index')
            ->with('success', "{$count} selected activity log(s) deleted successfully.");
    }

    /**
     * Delete all activity log records.
     */
    public function clearAll(): RedirectResponse
    {
        ActivityLog::query()->delete();

        return redirect()->route('admin.activity-logs.index')
            ->with('success', 'All activity log records cleared successfully.');
    }
}
