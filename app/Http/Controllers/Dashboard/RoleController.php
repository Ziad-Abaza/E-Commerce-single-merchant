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
    const PROTECTED_ROLES = ['owner', 'admin'];

    /**
     * Display a paginated listing of the roles with stats.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 20);
            $roles = Role::with(['permissions' => function ($query) {
                $query->select('permissions.id', 'permissions.name');
            }])->orderBy('name')->paginate($perPage);

            $permissions = Permission::all(['id', 'name']);

            $stats = [
                'total_roles' => Role::count(),
                'protected_roles' => Role::whereIn('name', self::PROTECTED_ROLES)->count(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Roles retrieved successfully.',
                'data' => [
                    'roles' => $roles->items(),
                    'permissions' => $permissions,
                ],
                'stats' => $stats,
                'pagination' => [
                    'total' => $roles->total(),
                    'current_page' => $roles->currentPage(),
                    'per_page' => $roles->perPage(),
                    'last_page' => $roles->lastPage(),
                    'from' => $roles->firstItem(),
                    'to' => $roles->lastItem(),
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error(__METHOD__ . ' - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve roles.',
                'errors' => [
                    'server' => [$e->getMessage()]
                ],
            ], 500);
        }
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
                'permissions.*' => 'exists:permissions,id',
            ]);

            DB::beginTransaction();

            $role = Role::create([
                'name' => $validated['name'],
                'guard_name' => config('auth.defaults.guard', 'sanctum'),
            ]);

            if (!empty($validated['permissions'])) {
                // Convert permission IDs to permission objects for syncing
                $permissions = Permission::whereIn('id', $validated['permissions'])->get();
                $this->authorizeSensitivePermissions($user, $permissions->pluck('name')->toArray());
                $role->syncPermissions($permissions);
            }

            DB::commit();

            Log::info('Role created', [
                'role_name' => $role->name,
                'created_by' => $user->id,
                'permissions_count' => count($validated['permissions'] ?? [])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully.',
                'data' => $role->load('permissions'),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role.',
                'errors' => ['server' => [$e->getMessage()]],
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
                    'success' => false,
                    'message' => 'Access denied.',
                    'errors' => ['role' => ['You cannot view the owner role.']],
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Role retrieved successfully.',
                'data' => $role,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found.',
                'errors' => ['role' => ['Role could not be found.']],
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve role.',
                'errors' => ['server' => [$e->getMessage()]],
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
                    'success' => false,
                    'message' => 'You cannot modify protected roles.',
                    'errors' => ['role' => ['Only the owner can modify this role.']],
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
                'permissions.*' => 'exists:permissions,id',
            ]);

            DB::beginTransaction();

            $role->update(['name' => $validated['name']]);

            if (!empty($validated['permissions'])) {
                // Convert permission IDs to permission objects for syncing
                $permissions = Permission::whereIn('id', $validated['permissions'])->get();
                $this->authorizeSensitivePermissions($user, $permissions->pluck('name')->toArray());
                $role->syncPermissions($permissions);
            }

            DB::commit();

            Log::info('Role updated', [
                'role_name' => $role->name,
                'updated_by' => $user->id,
                'permissions_count' => count($validated['permissions'] ?? [])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully.',
                'data' => $role->fresh('permissions'),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found.',
                'errors' => ['role' => ['Role could not be found.']],
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role.',
                'errors' => ['server' => [$e->getMessage()]],
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
                    'success' => false,
                    'message' => 'This role cannot be deleted.',
                    'errors' => ['role' => ['Protected roles cannot be deleted.']],
                ], 403);
            }

            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete role with assigned users.',
                    'errors' => ['role' => ['Please reassign users first.']],
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
                'success' => true,
                'message' => 'Role deleted successfully.',
                'data' => null,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found.',
                'errors' => ['role' => ['Role could not be found.']],
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role.',
                'errors' => ['server' => [$e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Authorize assigning sensitive permissions.
     */
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
     * Check if the current user is owner.
     */
    private function userIsOwner(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->isOwner();
    }
}
