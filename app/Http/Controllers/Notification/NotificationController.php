<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /**
         * @var \App\Models\User $user
         */
        $user = Auth::user();

        $notifications = $user->notifications()
            ->when($request->has('read'), function ($query) use ($request) {
                if ($request->read === 'false') {
                    return $query->whereNull('read_at');
                }
            })
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'data' => $notifications->items(),
            'success' => true,
            'message' => 'Notifications retrieved successfully.',
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ], 200);
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead($id)
    {
        /**
         * @var \App\Models\User $user
         */
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json(['message' => 'Notification not found.'], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read.',
            'success' => true,
        ], 200);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(
            [
                'message' => 'All notifications marked as read.',
                'success' => true,
            ],
            200
        );
    }

    /**
     * Remove the specified notification.
     */
    public function destroy($id)
    {
        /**
         * @var \App\Models\User $user
         */
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json(['message' => 'Notification not found.'], 404);
        }

        $notification->delete();

        return response()->json(
            [
                'message' => 'Notification deleted.',
                'success' => true,
            ],
            200
        );
    }

    /**
     * Remove all notifications.
     */
    public function destroyAll()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = Auth::user();
        $user->notifications()->delete();

        return response()->json([
            'message' => 'All notifications deleted.', 'success' => true,
        ], 200);
    }

    /**
     * Get count of unread notifications.
     */
    public function unreadCount()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = Auth::user();
        $count = $user->unreadNotifications()->count();
        return response()->json(['count' => $count, 'success' => true], 200);
    }
}
