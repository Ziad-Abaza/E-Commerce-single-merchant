<?php

namespace App\Http\Controllers\Notification;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use NotificationChannels\WebPush\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WebPushController extends Controller
{
    /**
     * Subscribe to push notifications.
     */
    public function subscribe(Request $request)
    {
        Log::info('[WebPushController@subscribe] Incoming request', $request->all());

        $request->validate([
            'endpoint' => 'required|url',
            'keys' => 'required|array',
        ]);

        if (!Auth::check()) {
            Log::warning('[WebPushController@subscribe] User not authenticated');
            return response()->json(['message' => 'User not authenticated.'], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->updatePushSubscription(
            $request->endpoint,
            $request->keys['p256dh'] ?? null,
            $request->keys['auth'] ?? null,
            $request->keys['encoding'] ?? 'aesgcm'
        );

        Log::info('[WebPushController@subscribe] Subscription saved for user', [
            'user_id' => $user->id,
            'endpoint' => $request->endpoint
        ]);

        return response()->json(['message' => 'Subscribed successfully.'], 201);
    }


    /**
     * Unsubscribe from push notifications.
     */
    public function unsubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
        ]);

        $subscription = PushSubscription::where('endpoint', $request->endpoint)->first();

        if ($subscription) {
            $subscription->delete();
            return response()->json(['message' => 'Unsubscribed successfully.'], 200);
        }

        return response()->json(['message' => 'Subscription not found.'], 404);
    }
}
