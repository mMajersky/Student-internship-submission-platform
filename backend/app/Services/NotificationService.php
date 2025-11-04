<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create a notification for a user
     */
    public static function create($userId, $type, $title, $message, $data = [])
    {
        if (!$userId) {
            \Log::warning('Attempted to create notification with null userId', ['type' => $type, 'title' => $title]);
            return null;
        }

        try {
            $notification = Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ]);

            \Log::info('Notification created', [
                'notification_id' => $notification->id,
                'user_id' => $userId,
                'type' => $type,
            ]);

            return $notification;
        } catch (\Exception $e) {
            \Log::error('Failed to create notification', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Create notification and optionally send email
     */
    public static function createAndNotify($userId, $type, $title, $message, $data = [], $emailClass = null, $emailData = [])
    {
        // Always create system notification
        $notification = self::create($userId, $type, $title, $message, $data);

        // Send email only if user has email notifications enabled
        if ($emailClass && $notification) {
            $user = User::find($userId);
            
            if ($user && $user->email_notifications && $user->email) {
                \Log::info('Sending email notification', ['user_id' => $userId, 'email' => $user->email]);
                \App\Services\EmailService::send($emailClass, $user->email, $emailData);
            }
        }

        return $notification;
    }

    /**
     * Get unread notifications count for a user
     */
    public static function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)->where('is_read', false)->count();
    }

    /**
     * Mark all notifications as read for a user
     */
    public static function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }
}

