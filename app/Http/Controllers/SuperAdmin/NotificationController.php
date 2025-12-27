<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the current super admin
     */
    public function index(Request $request)
    {
        $admin = auth('super_admin')->user();

        if ($request->ajax() || $request->wantsJson()) {
            $notifications = $admin->notifications()
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json(['notifications' => $notifications]);
        }

        $notifications = $admin->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('super-admin.notifications.index', compact('notifications'));
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        $count = auth('super_admin')->user()->unreadNotifications->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = auth('super_admin')->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        auth('super_admin')->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        auth('super_admin')->user()->notifications()->findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}

