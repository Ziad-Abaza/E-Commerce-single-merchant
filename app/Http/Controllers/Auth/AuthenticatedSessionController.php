<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        /**
         * @var \App\Models\User
         */
        $user = Auth::guard('web')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Authentication failed.',
                'success' => false,
            ], 401);
        }

        $isResendEmail = false;
        if (!$user->hasVerifiedEmail() && env('APP_ENV') === 'production') {
            $user->sendEmailVerificationNotification();
            $isResendEmail = true;
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'is_active' => $user->is_active,
                'is_verified' => $user->email_verified_at ? true : false,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'avatar_url' => $user->getAvatarUrl() ?? null,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],
            'token' => $token,
            'success' => true,
            'resend_email' => $isResendEmail,
        ], 200);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user) {
            $accessToken = $user->currentAccessToken();

            if ($accessToken && method_exists($accessToken, 'delete')) {
                $accessToken->delete();
            }
        }

        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            'message' => 'Logged out successfully.',
            'success' => true,
        ], 200);
    }
}
