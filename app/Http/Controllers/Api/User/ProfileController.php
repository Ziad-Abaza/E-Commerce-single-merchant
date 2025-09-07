<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\ChangePasswordRequest;

class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile.
     */
    public function show(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'User not authenticated.',
                    'data' => null,
                    'errors' => ['auth' => ['User session expired or invalid.']],
                    'code' => 401,
                ], 401);
            }

            // Load additional relations if needed
            $user->loadCount(['orders', 'reviews', 'wishlistItems']);

            return response()->json([
                'message' => 'Profile retrieved successfully.',
                'data' => new UserResource($user),
                'errors' => null,
                'code' => 200,
                'success' => true,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found.',
                'data' => null,
                'errors' => ['user' => ['User could not be found.']],
                'code' => 404,
                'success' => false,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
                'success' => false,
            ], 500);
        }
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(ProfileUpdateRequest $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthorized. Please log in.',
                    'data' => null,
                    'errors' => ['auth' => ['Authentication required.']],
                    'code' => 401,
                    'success' => false,
                ], 401);
            }

            $data = $request->validated();

            // Prevent email updates
            unset($data['email']);

            DB::beginTransaction();

            $user->fill($data);
            $user->save();

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $user->setAvatar($request->file('avatar'));
            }

            DB::commit();

            return response()->json([
                'message' => 'Profile updated successfully.',
                'data' => new UserResource($user->fresh()),
                'errors' => null,
                'code' => 200,
                'success' => true,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update profile.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
                'success' => false,
            ], 500);
        }
    }

    /**
     * Change the authenticated user's password.
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthorized. Please log in.',
                    'data' => null,
                    'errors' => ['auth' => ['Authentication required.']],
                    'code' => 401,
                    'success' => false,
                ], 401);
            }

            $data = $request->validated();

            // Verify current password
            if (!Hash::check($data['current_password'], $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect.',
                    'data' => null,
                    'errors' => ['current_password' => ['The provided password does not match our records.']],
                    'code' => 422,
                    'success' => false,
                ], 422);
            }

            // Prevent setting same password
            if (Hash::check($data['password'], $user->password)) {
                return response()->json([
                    'message' => 'New password cannot be the same as current password.',
                    'data' => null,
                    'errors' => ['password' => ['New password must be different from current password.']],
                    'code' => 422,
                    'success' => false,
                ], 422);
            }

            DB::beginTransaction();
            $user->password = Hash::make($data['password']);
            $user->save();
            DB::commit();

            return response()->json([
                'message' => 'Password changed successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
                'success' => true,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to change password.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
                'success' => false,
            ], 500);
        }
    }

    /**
     * Get user statistics
     */
    public function getStats(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'User not authenticated.',
                    'data' => null,
                    'errors' => ['auth' => ['User session expired or invalid.']],
                    'code' => 401,
                    'success' => false,
                ], 401);
            }

            $stats = [
                'total_orders' => $user->orders()->count(),
                'total_reviews' => $user->reviews()->count(),
                'total_wishlist_items' => $user->wishlistItems()->count(),
                'total_spent' => $user->orders()->where('status', 'completed')->sum('total_amount'),
                'member_since' => $user->created_at->diffForHumans(),
            ];

            return response()->json([
                'message' => 'User statistics retrieved successfully.',
                'data' => $stats,
                'errors' => null,
                'code' => 200,
                'success' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve user statistics.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
                'success' => false,
            ], 500);
        }
    }
}
