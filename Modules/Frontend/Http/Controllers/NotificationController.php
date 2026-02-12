<?php

namespace Modules\Frontend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
            public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Mark all notifications as read when user visits the page
        $user->unreadNotifications()->update(['read_at' => now()]);

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->get(); // Show all notifications

        $all_unread_count = 0; // All notifications are now read

        return view('frontend::notifications.list', compact('notifications', 'all_unread_count'));
    }

    public function markAllAsRead()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
        }

        $user->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['status' => 'success', 'message' => 'All notifications marked as read']);
    }

    public function markAsRead($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
        }

        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['status' => 'success', 'message' => 'Notification marked as read']);
        }

        return response()->json(['status' => 'error', 'message' => 'Notification not found'], 404);
    }
}
