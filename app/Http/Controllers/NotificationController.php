<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display listing of user notifications.
     */
    public function index(Request $request): View
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->orderBy('id', 'desc')
            ->paginate(15);

        // Auto-mark listed unread notifications as read
        Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Notification $notification, Request $request): RedirectResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Clear all notifications for the authenticated user.
     */
    public function clearAll(Request $request): RedirectResponse
    {
        Notification::where('user_id', $request->user()->id)->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'All notifications cleared successfully.');
    }
}
