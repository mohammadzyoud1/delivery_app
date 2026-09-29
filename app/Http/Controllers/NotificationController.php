<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // ==================== Notifications ====================

    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = $user
            ->notifications()
            ->latest()
            ->get();

        $unreadCount = $user
            ->notifications()
            ->whereNull('read_at')
            ->count();

        return inertia('Notifications/Notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    // ==================== Mark As Read ====================

    public function markAsRead(
        Request $request,
        int $notification,
    ) {
        $notification = $request
            ->user()
            ->notifications()
            ->findOrFail($notification);

        $notification->update([
            'read_at' => now(),
        ]);

        return back();
    }
}
