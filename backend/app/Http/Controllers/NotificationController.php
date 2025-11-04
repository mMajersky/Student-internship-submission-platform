<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Get all notifications for authenticated user
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            $query = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc');

            // Filter by read/unread
            if ($request->has('unread_only') && $request->unread_only) {
                $query->where('is_read', false);
            }

            // Pagination
            $perPage = $request->get('per_page', 20);
            $notifications = $query->paginate($perPage);

            return response()->json($notifications, 200);

        } catch (\Exception $e) {
            Log::error('Error fetching notifications: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching notifications.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount()
    {
        try {
            $user = Auth::user();
            
            $count = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();

            return response()->json([
                'unread_count' => $count
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error getting unread count: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        try {
            $user = Auth::user();
            
            $notification = Notification::where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();

            $notification->markAsRead();

            return response()->json([
                'message' => 'Notification marked as read.',
                'notification' => $notification
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Notification not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $user = Auth::user();
            
            $updated = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            return response()->json([
                'message' => 'All notifications marked as read.',
                'updated_count' => $updated
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            
            $notification = Notification::where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();

            $notification->delete();

            return response()->json([
                'message' => 'Notification deleted.'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Notification not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}

