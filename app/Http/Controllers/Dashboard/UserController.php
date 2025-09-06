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

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json([
            'message' => 'User list retrieved successfully.',
            'data' => UserResource::collection($users),
            'errors' => null,
            'code' => 200,
        ], 200);
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json([
                'message' => 'User retrieved successfully.',
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
        }
    }

    public function store(UserStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $data['is_active'] = $data['is_active'] ?? true;
            $data['password'] = Hash::make($data['password']);
            
            DB::beginTransaction();
            $user = User::create($data);
            DB::commit();

            if($request->hasFile('avatar') ) {
                $user->setAvatar($request->file('avatar'));
            }

            return response()->json([
                'message' => 'User created successfully.',
                'data' => new UserResource($user),
                'errors' => null,
                'code' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create user.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

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
            DB::commit();
            
            if($request->hasFile('avatar') ) {
                $user->setAvatar($request->file('avatar'));
            }

            return response()->json([
                'message' => 'User updated successfully.',
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
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update user.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            $user->setAvatar(null);
            return response()->json([
                'message' => 'User deleted successfully.',
                'data' => null,
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
                'message' => 'Failed to delete user.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }
}
