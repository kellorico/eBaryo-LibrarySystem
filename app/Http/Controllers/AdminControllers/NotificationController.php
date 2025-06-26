<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Get all notifications for admin
     */
    public function index()
    {
        $notifications = Notification::orderBy('created_at', 'desc')
            ->paginate(20);

        // Add time_ago, icon, color_class to each notification
        $notifications->getCollection()->transform(function ($notification) {
            return array_merge($notification->toArray(), [
                'time_ago' => $notification->time_ago,
                'icon' => $notification->icon,
                'color_class' => $notification->color_class,
                'created_at_exact' => $notification->created_at->toDateTimeString(),
            ]);
        });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Notification::unread()->count(),
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount()
    {
        $count = Notification::unread()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => Notification::unread()->count(),
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return response()->json([
            'success' => true,
            'unread_count' => Notification::unread()->count(),
        ]);
    }

    /**
     * Clear all notifications
     */
    public function clearAll()
    {
        Notification::truncate();

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    /**
     * Get recent activity for dashboard
     */
    public function recentActivity()
    {
        $activities = Notification::orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'icon' => $notification->icon,
                    'color_class' => $notification->color_class,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'time_ago' => $notification->time_ago,
                ];
            });
        return response()->json(['activities' => $activities]);
    }
}
