<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user.
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Get recent notifications for the notification dropdown.
     */
    public function recent()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unreadCount = Auth::user()->unreadNotifications()->count();

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                    'message' => method_exists($notification, 'getMessage') ? $notification->getMessage() : 'Nueva notificación',
                    'url' => method_exists($notification, 'getUrl') ? $notification->getUrl() : null,
                    'icon' => method_exists($notification, 'getIcon') ? $notification->getIcon() : 'bell',
                    'color' => method_exists($notification, 'getColor') ? $notification->getColor() : 'gray',
                ];
            }),
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Delete a notification.
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Delete all notifications.
     */
    public function destroyAll()
    {
        Auth::user()->notifications()->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get notification settings for the authenticated user.
     */
    public function settings()
    {
        $user = Auth::user();
        $profile = $user->profile;

        $settings = [
            'email_notifications' => $user->email_notifications ?? true,
            'browser_notifications' => $user->browser_notifications ?? true,
            'notification_types' => [
                'new_follower' => $profile->notify_new_follower ?? true,
                'new_message' => $profile->notify_new_message ?? true,
                'comment_reply' => $profile->notify_comment_reply ?? true,
                'comment_liked' => $profile->notify_comment_liked ?? true,
                'mentioned' => $profile->notify_mentioned ?? true,
            ],
        ];

        return view('notifications.settings', compact('settings'));
    }

    /**
     * Update notification settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'browser_notifications' => 'boolean',
            'notification_types' => 'array',
            'notification_types.*' => 'boolean',
        ]);

        $user = Auth::user();
        $profile = $user->profile;

        // Update user settings
        $user->update([
            'email_notifications' => $request->input('email_notifications', true),
            'browser_notifications' => $request->input('browser_notifications', true),
        ]);

        // Update profile notification preferences
        $notificationTypes = $request->input('notification_types', []);
        $profile->update([
            'notify_new_follower' => $notificationTypes['new_follower'] ?? true,
            'notify_new_message' => $notificationTypes['new_message'] ?? true,
            'notify_comment_reply' => $notificationTypes['comment_reply'] ?? true,
            'notify_comment_liked' => $notificationTypes['comment_liked'] ?? true,
            'notify_mentioned' => $notificationTypes['mentioned'] ?? true,
        ]);

        return response()->json(['success' => true]);
    }
}