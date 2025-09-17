<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Resend the email verification link.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['status' => 'email-already-verified']);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['status' => 'verification-link-sent', 'message' => 'A new verification link has been sent to the email address you provided during registration.', 'success' => true, 'code' => 200]);
    }
}
