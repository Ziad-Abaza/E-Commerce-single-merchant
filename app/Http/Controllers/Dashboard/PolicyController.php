<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PolicyController extends Controller
{
    /**
     * Display a listing of policies with filters and pagination
     */
    public function index(Request $request)
    {
        $request->validate([
            'type' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100',
            'search' => 'nullable|string|max:255',
        ]);

        $query = Policy::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $perPage = $request->per_page ?? 15;
        $policies = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $policies->items(),
            'pagination' => [
                'total' => $policies->total(),
                'per_page' => $policies->perPage(),
                'current_page' => $policies->currentPage(),
                'last_page' => $policies->lastPage(),
            ],
            'types' => Policy::select('type')->distinct()->pluck('type'),
        ]);
    }

    /**
     * Store a newly created policy
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $policy = Policy::create([
                'type' => $request->type,
                'title' => $request->title,
                'content' => $request->input('content'),
                'is_active' => $request->boolean('is_active', true),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Policy created successfully',
                'data' => $policy,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating policy: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create policy',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Display the specified policy
     */
    public function show($id)
    {
        try {
            $policy = Policy::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $policy,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Policy not found',
            ], 404);
        }
    }

    /**
     * Update the specified policy
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|required|string|max:50',
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $policy = Policy::findOrFail($id);

            $policy->update($request->only(['type', 'title', 'content', 'is_active']));

            return response()->json([
                'success' => true,
                'message' => 'Policy updated successfully',
                'data' => $policy,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Policy not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error updating policy: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update policy',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Toggle policy active status
     */
    public function toggleStatus($id)
    {
        try {
            $policy = Policy::findOrFail($id);
            $policy->update(['is_active' => !$policy->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Policy status updated successfully',
                'data' => $policy,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Policy not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error toggling policy status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update policy status',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Remove the specified policy
     */
    public function destroy($id)
    {
        try {
            $policy = Policy::findOrFail($id);
            $policy->delete();

            return response()->json([
                'success' => true,
                'message' => 'Policy deleted successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Policy not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting policy: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete policy',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
