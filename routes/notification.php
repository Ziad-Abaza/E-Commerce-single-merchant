<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Notification\WebPushController;
use App\Http\Controllers\Notification\NotificationController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Add these routes to handle push subscriptions
    Route::post('/push/subscribe', [WebPushController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [WebPushController::class, 'unsubscribe'])->name('push.unsubscribe');
});

// Notifications API
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::delete('/notifications', [NotificationController::class, 'destroyAll']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
});
