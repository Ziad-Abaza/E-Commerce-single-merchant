<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\WishlistCategory;
use App\Http\Resources\WishlistCategoryResource;
use App\Http\Requests\WishlistCategoryStoreRequest;
use App\Http\Requests\WishlistCategoryUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WishlistCategoryController extends Controller
{
    /**
     * Display a listing of the wishlist categories.
     */
    public function index(Request $request)
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json([
                    'message' => 'User not authenticated',
                    'data' => null,
                    'errors' => ['auth' => ['User must be logged in']],
                    'code' => 401,
                ], 401);
            }

            $query = WishlistCategory::with(['user', 'items.product'])
                ->where('user_id', $userId);

            // Filter by default status if specified
            if ($request->has('is_default')) {
                $query->where('is_default', $request->boolean('is_default'));
            }

            $wishlistCategories = $query->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate($request->per_page ?? 15);

            return response()->json([
                'message' => 'Wishlist categories retrieved successfully.',
                'data' => WishlistCategoryResource::collection($wishlistCategories),
                'pagination' => [
                    'current_page' => $wishlistCategories->currentPage(),
                    'last_page' => $wishlistCategories->lastPage(),
                    'per_page' => $wishlistCategories->perPage(),
                    'total' => $wishlistCategories->total(),
                ],
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve wishlist categories.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Display the specified wishlist category.
     */
    public function show($id)
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json([
                    'message' => 'User not authenticated',
                    'data' => null,
                    'errors' => ['auth' => ['User must be logged in']],
                    'code' => 401,
                ], 401);
            }

            $wishlistCategory = WishlistCategory::with(['user', 'items.product'])
                ->where('user_id', $userId)
                ->findOrFail($id);

            return response()->json([
                'message' => 'Wishlist category retrieved successfully.',
                'data' => new WishlistCategoryResource($wishlistCategory),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Wishlist category not found.',
                'data' => null,
                'errors' => ['wishlist_category' => ['Wishlist category could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve wishlist category.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Store a newly created wishlist category.
     */
    public function store(WishlistCategoryStoreRequest $request)
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json([
                    'message' => 'User not authenticated',
                    'data' => null,
                    'errors' => ['auth' => ['User must be logged in']],
                    'code' => 401,
                ], 401);
            }

            DB::beginTransaction();

            $data = $request->validated();

            // Override user_id with authenticated user
            $data['user_id'] = $userId;

            if (!empty($data['is_default'])) {
                WishlistCategory::where('user_id', $userId)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            } else {
                $data['is_default'] = false;
            }

            $wishlistCategory = WishlistCategory::create($data);

            // Handle file uploads
            if ($request->hasFile('icon')) {
                $wishlistCategory->setIcon($request->file('icon'));
            }
            if ($request->hasFile('image')) {
                $wishlistCategory->setImage($request->file('image'));
            }

            DB::commit();

            return response()->json([
                'message' => 'Wishlist category created successfully.',
                'data' => new WishlistCategoryResource($wishlistCategory->load(['user', 'items.product'])),
                'errors' => null,
                'code' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create wishlist category.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Update the specified wishlist category.
     */
    public function update(WishlistCategoryUpdateRequest $request, $id)
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json([
                    'message' => 'User not authenticated',
                    'data' => null,
                    'errors' => ['auth' => ['User must be logged in']],
                    'code' => 401,
                ], 401);
            }

            $wishlistCategory = WishlistCategory::where('user_id', $userId)->findOrFail($id);

            DB::beginTransaction();

            $data = $request->validated();

            if (!empty($data['is_default']) && !$wishlistCategory->is_default) {
                WishlistCategory::where('user_id', $userId)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            } elseif (!isset($data['is_default'])) {
                $data['is_default'] = $wishlistCategory->is_default;
            }

            if ($wishlistCategory->name === 'Favorites') {
                unset($data['name']);
            }

            $wishlistCategory->update($data);

            if ($request->hasFile('icon')) {
                $wishlistCategory->setIcon($request->file('icon'));
            }
            if ($request->hasFile('image')) {
                $wishlistCategory->setImage($request->file('image'));
            }

            DB::commit();

            return response()->json([
                'message' => 'Wishlist category updated successfully.',
                'data' => new WishlistCategoryResource($wishlistCategory->fresh(['user', 'items.product'])),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Wishlist category not found.',
                'data' => null,
                'errors' => ['wishlist_category' => ['Wishlist category could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update wishlist category.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Remove the specified wishlist category.
     */
    public function destroy($id)
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json([
                    'message' => 'User not authenticated',
                    'data' => null,
                    'errors' => ['auth' => ['User must be logged in']],
                    'code' => 401,
                ], 401);
            }

            $wishlistCategory = WishlistCategory::where('user_id', $userId)->findOrFail($id);

            if ($wishlistCategory->name === 'Favorites') {
                return response()->json([
                    'message' => 'The "Favorites" category cannot be deleted.',
                    'data' => null,
                    'errors' => ['wishlist_category' => ['Favorites category is protected.']],
                    'code' => 403,
                ], 403);
            }

            // Check if it's a default category
            if ($wishlistCategory->is_default) {
                return response()->json([
                    'message' => 'Default wishlist categories cannot be deleted.',
                    'data' => null,
                    'errors' => ['wishlist_category' => ['Default wishlist categories are protected.']],
                    'code' => 403,
                ], 403);
            }

            DB::beginTransaction();

            // Delete all items in this category
            $wishlistCategory->items()->delete();

            // Delete category
            $wishlistCategory->delete();

            // Clean up media
            $wishlistCategory->clearMediaCollection('icon');
            $wishlistCategory->clearMediaCollection('image');

            DB::commit();

            return response()->json([
                'message' => 'Wishlist category deleted successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Wishlist category not found.',
                'data' => null,
                'errors' => ['wishlist_category' => ['Wishlist category could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete wishlist category.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Get default category for user
     */
    public function getDefaultCategory()
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json([
                    'message' => 'User not authenticated',
                    'data' => null,
                    'errors' => ['auth' => ['User must be logged in']],
                    'code' => 401,
                ], 401);
            }

            $defaultCategory = WishlistCategory::with(['user', 'items.product'])
                ->where('user_id', $userId)
                ->where('is_default', true)
                ->first();

            // Create default category if it doesn't exist
            if (!$defaultCategory) {
                $defaultCategory = WishlistCategory::create([
                    'user_id' => $userId,
                    'name' => 'Favorites',
                    'is_default' => true
                ]);
            }

            return response()->json([
                'message' => 'Default wishlist category retrieved successfully.',
                'data' => new WishlistCategoryResource($defaultCategory),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve default wishlist category.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }
}
