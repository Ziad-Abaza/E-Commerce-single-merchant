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
            return response()->json(['status' => 'email-already-verified', 'message' => 'Your email is already verified.', 'success' => true, 'code' => 200, 'sent' => false]);
        }

        $user->sendEmailVerificationNotification();
        return response()->json(['status' => 'verification-link-sent', 'message' => 'A new verification link has been sent to the email address you provided during registration, if not received please contact us.', 'success' => true, 'code' => 200, 'sent' => true]);
    }
}
