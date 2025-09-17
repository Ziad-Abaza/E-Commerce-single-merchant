<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

//  api Auth Routes
Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('register');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth:sanctum', 'signed', 'throttle:6,1'])
    ->name('verification.verify')
    ->where('id', '[0-9]+')
    ->where('hash', '[a-zA-Z0-9]+');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth:sanctum', 'throttle:6,1'])
    ->name('verification.send');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum')
    ->name('logout');


Route::post('/email/verify-link', function (Request $request) {
    $request->validate(['url' => 'required|url']);
    $signedUrl = $request->url;

    // تحليل الرابط
    $parsedUrl = parse_url($signedUrl);
    parse_str($parsedUrl['query'] ?? '', $query);

    $userId = $query['id'] ?? null;
    $hash = $query['hash'] ?? null;

    if (!$userId || !$hash) {
        return response()->json(['success' => false, 'message' => 'Invalid verification link.']);
    }

    // تحقق من صلاحية الرابط
    if (! URL::hasValidSignature($request->merge(['id' => $userId, 'hash' => $hash]))) {
        return response()->json(['success' => false, 'message' => 'Invalid or expired verification link.']);
    }

    $user = \App\Models\User::find($userId);
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User not found.']);
    }

    if ($user->hasVerifiedEmail()) {
        return response()->json(['success' => true, 'message' => 'Email already verified.']);
    }

    $user->markEmailAsVerified();

    return response()->json(['success' => true, 'message' => 'Email verified successfully.']);
});
