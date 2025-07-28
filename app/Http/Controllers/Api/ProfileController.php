<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Resources\UserResource;

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

            return response()->json([
                'message' => 'Profile retrieved successfully.',
                'data' => new UserResource($user),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found.',
                'data' => null,
                'errors' => ['user' => ['User could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthorized. Please log in.',
                    'data' => null,
                    'errors' => ['auth' => ['Authentication required.']],
                    'code' => 401,
                ], 401);
            }

            $data = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:8192',
            ]);

            DB::beginTransaction();

            $user->fill($data);
            $user->save();

            if ($request->hasFile('avatar')) {
                $user->setAvatar($request->file('avatar'));
            }

            DB::commit();

            return response()->json([
                'message' => 'Profile updated successfully.',
                'data' => new UserResource($user),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'data' => null,
                'errors' => $e->errors(),
                'code' => 422,
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update profile.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Change the authenticated user's password.
     */
    public function changePassword(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthorized. Please log in.',
                    'data' => null,
                    'errors' => ['auth' => ['Authentication required.']],
                    'code' => 401,
                ], 401);
            }

            $request->validate([
                'current_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect.',
                    'data' => null,
                    'errors' => ['current_password' => ['The provided password does not match our records.']],
                    'code' => 422,
                ], 422);
            }

            DB::beginTransaction();
            $user->password = Hash::make($request->password);
            $user->save();
            DB::commit();

            return response()->json([
                'message' => 'Password changed successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'data' => null,
                'errors' => $e->errors(),
                'code' => 422,
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to change password.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }
}
