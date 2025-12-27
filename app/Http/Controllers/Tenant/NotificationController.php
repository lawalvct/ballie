<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the current user
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $notifications = Auth::user()->notifications()
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json(['notifications' => $notifications]);
        }

        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('tenant.notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        $count = Auth::user()->unreadNotifications->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        Auth::user()->notifications()->findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}
