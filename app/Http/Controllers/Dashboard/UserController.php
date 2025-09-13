<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users with pagination, filters and sorting
     */
    public function index(Request $request)
    {
        try {
            $query = User::query();

            // Filtering
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'id');
            $sortDir = $request->get('sort_dir', 'desc');
            $query->orderBy($sortBy, $sortDir);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $users = $query->paginate($perPage);

            // Statistics
            $statistics = [
                'total_users'      => User::count(),
                'active_users'     => User::where('is_active', true)->count(),
                'inactive_users'   => User::where('is_active', false)->count(),
                'trashed_users'    => User::onlyTrashed()->count(),
                'verified_users'   => User::whereNotNull('email_verified_at')->count(),
                'unverified_users' => User::whereNull('email_verified_at')->count(),
            ];

            $allRoles = Role::get(['id', 'name'])->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name
                ];
            });

            return response()->json([
                'success'     => true,
                'message'     => 'User list retrieved successfully.',
                'data'        => UserResource::collection($users),
                'pagination'  => [
                    'current_page' => $users->currentPage(),
                    'per_page'     => $users->perPage(),
                    'total'        => $users->total(),
                    'last_page'    => $users->lastPage(),
                ],
                'statistics'  => $statistics,
                'roles'       => $allRoles,
                'errors'      => null,
                'code'        => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user list.',
                'data'    => null,
                'errors'  => ['server' => [$e->getMessage()]],
                'code'    => 500,
            ], 500);
        }
    }


    /**
     * Display a single user details
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'User retrieved successfully.',
                'data' => new UserResource($user),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'data' => null,
                'errors' => ['user' => ['User could not be found.']],
                'code' => 404,
            ], 404);
        }
    }

    /**
     * Store a new user
     */
    public function store(UserStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $data['is_active'] = $data['is_active'] ?? true;
            $data['password'] = Hash::make($data['password']);

            DB::beginTransaction();
            $user = User::create($data);

            if ($request->hasFile('avatar')) {
                $user->setAvatar($request->file('avatar'));
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'data' => new UserResource($user),
                'errors' => null,
                'code' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Update a user
     */
    public function update(UserUpdateRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $data = $request->validated();
            $data['is_active'] = $data['is_active'] ?? true;

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            DB::beginTransaction();
            $user->update($data);

            if ($request->hasFile('avatar')) {
                $user->setAvatar($request->file('avatar'));
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'data' => new UserResource($user),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'data' => null,
                'errors' => ['user' => ['User could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Soft delete a user
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'data' => null,
                'errors' => ['user' => ['User could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Get trashed users (soft deleted)
     */
    public function trashed(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $users = User::onlyTrashed()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Trashed users retrieved successfully.',
            'data' => UserResource::collection($users),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
            'errors' => null,
            'code' => 200,
        ], 200);
    }

    /**
     * Restore a trashed user
     */
    public function restore($id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);
            $user->restore();
            return response()->json([
                'success' => true,
                'message' => 'User restored successfully.',
                'data' => new UserResource($user),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found in trash.',
                'data' => null,
                'errors' => ['user' => ['User could not be found in trash.']],
                'code' => 404,
            ], 404);
        }
    }

    /**
     * Force delete a user
     */
    public function forceDelete($id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);
            $user->forceDelete();
            return response()->json([
                'success' => true,
                'message' => 'User permanently deleted.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found in trash.',
                'data' => null,
                'errors' => ['user' => ['User could not be found in trash.']],
                'code' => 404,
            ], 404);
        }
    }

    /**
     * Assign a role to a user
     */
    public function assignRole(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $role = $request->get('role');

            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role is required.',
                    'errors' => ['role' => ['Role field is missing.']],
                    'code' => 422,
                ], 422);
            }

            $roles = is_array($role) ? $role : [$role];

            $user->syncRoles($roles);

            return response()->json([
                'success' => true,
                'message' => "Role(s) assigned successfully.",
                'data' => new UserResource($user),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'errors' => ['user' => ['User could not be found.']],
                'code' => 404,
            ], 404);
        }
    }

    /**
     * Remove a role from a user
     */
    public function removeRole(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $role = $request->get('role');

            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role is required.',
                    'errors' => ['role' => ['Role field is missing.']],
                    'code' => 422,
                ], 422);
            }

            $roles = is_array($role) ? $role : [$role];

            foreach ($roles as $r) {
                $user->removeRole($r);
            }

            return response()->json([
                'success' => true,
                'message' => "Role(s) removed successfully.",
                'data' => new UserResource($user),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'errors' => ['user' => ['User could not be found.']],
                'code' => 404,
            ], 404);
        }
    }
}
