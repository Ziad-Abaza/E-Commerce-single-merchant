<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    const PROTECTED_ROLES = ['owner', 'admin', 'superAdmin'];

    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        try {
            $roles = Role::with('permissions')->get();

            return response()->json([
                'message' => 'Roles list retrieved successfully.',
                'data' => $roles,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            Log::error(__METHOD__ . ' - ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve roles.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Get all available permissions.
     */
    public function permissions()
    {
        $permissions = Permission::all()->pluck('name');

        return response()->json([
            'message' => 'Available permissions retrieved successfully.',
            'data' => $permissions,
            'errors' => null,
            'code' => 200,
        ], 200);
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:roles,name',
                    'regex:/^[a-zA-Z0-9_-]+$/',
                    function ($attribute, $value, $fail) use ($user) {
                        if (preg_match('/^(system|root|master|god)/i', $value)) {
                            $fail('Role names starting with system, root, master, or god are not allowed.');
                        }

                        if (strtolower($value) === 'owner') {
                            if (!$user->isOwner()) {
                                $fail('Only the owner can create an owner role.');
                            }

                            if (Role::where('name', 'owner')->exists()) {
                                $fail('Only one owner role is allowed.');
                            }
                        }
                    }
                ],
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,name',
            ]);

            DB::beginTransaction();

            $role = Role::create([
                'name' => $validated['name'],
                'guard_name' => 'web',
            ]);

            if (!empty($validated['permissions'])) {
                $this->authorizeSensitivePermissions($user, $validated['permissions']);
                $role->syncPermissions($validated['permissions']);
            }

            DB::commit();

            Log::info('Role created', [
                'role_name' => $role->name,
                'created_by' => $user->id,
                'permissions_count' => count($validated['permissions'] ?? [])
            ]);

            return response()->json([
                'message' => 'Role created successfully.',
                'data' => $role->load('permissions'),
                'errors' => null,
                'code' => 201,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'data' => null,
                'errors' => $e->errors(),
                'code' => 422,
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create role.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Display the specified role.
     */
    public function show($id)
    {
        try {
            $role = Role::with('permissions')->findOrFail($id);

            if ($role->name === 'owner' && !$this->userIsOwner()) {
                return response()->json([
                    'message' => 'Access denied.',
                    'data' => null,
                    'errors' => ['role' => ['You cannot view the owner role.']],
                    'code' => 403,
                ], 403);
            }

            return response()->json([
                'message' => 'Role retrieved successfully.',
                'data' => $role,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Role not found.',
                'data' => null,
                'errors' => ['role' => ['Role could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve role.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = $request->user();
            $role = Role::findOrFail($id);

            if (in_array($role->name, self::PROTECTED_ROLES) && !$user->isOwner()) {
                return response()->json([
                    'message' => 'You cannot modify protected roles.',
                    'data' => null,
                    'errors' => ['role' => ['Only the owner can modify this role.']],
                    'code' => 403,
                ], 403);
            }

            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:roles,name,' . $id,
                    'regex:/^[a-zA-Z0-9_-]+$/',
                    function ($attribute, $value, $fail) use ($role, $user) {
                        if (in_array($role->name, self::PROTECTED_ROLES) && $value !== $role->name) {
                            $fail("The name of a protected role cannot be changed.");
                        }

                        if (strtolower($value) === 'owner' && !$user->isOwner()) {
                            $fail('Only the owner can assign the owner role.');
                        }
                    }
                ],
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,name',
            ]);

            DB::beginTransaction();

            $role->update(['name' => $validated['name']]);

            if (!empty($validated['permissions'])) {
                $this->authorizeSensitivePermissions($user, $validated['permissions']);
                $role->syncPermissions($validated['permissions']);
            }

            DB::commit();

            Log::info('Role updated', [
                'role_name' => $role->name,
                'updated_by' => $user->id,
                'permissions_count' => count($validated['permissions'] ?? [])
            ]);

            return response()->json([
                'message' => 'Role updated successfully.',
                'data' => $role->fresh('permissions'),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Role not found.',
                'data' => null,
                'errors' => ['role' => ['Role could not be found.']],
                'code' => 404,
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'data' => null,
                'errors' => $e->errors(),
                'code' => 422,
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update role.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();
            $role = Role::findOrFail($id);

            if (in_array($role->name, self::PROTECTED_ROLES)) {
                return response()->json([
                    'message' => 'This role cannot be deleted.',
                    'data' => null,
                    'errors' => ['role' => ['Protected roles cannot be deleted.']],
                    'code' => 403,
                ], 403);
            }

            if ($role->users()->count() > 0) {
                return response()->json([
                    'message' => 'Cannot delete role with assigned users.',
                    'data' => null,
                    'errors' => ['role' => ['Please reassign users first.']],
                    'code' => 400,
                ], 400);
            }

            DB::beginTransaction();
            $role->delete();
            DB::commit();

            Log::info('Role deleted', [
                'role_name' => $role->name,
                'deleted_by' => $user->id
            ]);

            return response()->json([
                'message' => 'Role deleted successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Role not found.',
                'data' => null,
                'errors' => ['role' => ['Role could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete role.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    private function authorizeSensitivePermissions($user, array $permissions)
    {
        $sensitivePermissions = [
            'assign_owner',
            'assign_admin',
            'assign_superAdmin',
            'manage_roles',
            'manage_permissions',
        ];

        $requested = array_intersect($permissions, $sensitivePermissions);

        foreach ($requested as $perm) {
            if (!$user->can($perm)) {
                throw new \Exception("You do not have permission to assign '{$perm}'.");
            }
        }
    }

    /**
     * check if user has permission to assign role
     */
    private function userIsOwner(): bool
    {
        $user = Auth::user();

        /** @var \App\Models\User $user */

        return $user && $user->isOwner();
    }
}
