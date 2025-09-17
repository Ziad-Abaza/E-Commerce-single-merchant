<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\SignatureException;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request)
    {

        $user = User::findOrFail($request->route('id'));

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.',
                'success' => true
            ]);
        }

        if (! URL::hasValidSignature($request)) {
            return response()->json([
                'message' => 'Invalid or expired verification link.',
                'success' => false
            ], 400);
        }

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return response()->json([
                'message' => 'Invalid verification link.',
                'success' => false
            ], 400);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json([
            'message' => 'Email has been verified successfully!',
            'success' => true
        ]);
    }
}
