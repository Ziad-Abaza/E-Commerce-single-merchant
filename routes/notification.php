<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebPushController;

// Add these routes to handle push subscriptions
Route::post('/push/subscribe', [WebPushController::class, 'subscribe'])->name('push.subscribe');
Route::post('/push/unsubscribe', [WebPushController::class, 'unsubscribe'])->name('push.unsubscribe');
