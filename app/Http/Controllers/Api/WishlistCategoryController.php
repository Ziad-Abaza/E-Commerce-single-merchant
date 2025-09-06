<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WishlistCategory;
use App\Http\Resources\WishlistCategoryResource;
use App\Http\Requests\WishlistCategoryStoreRequest;
use App\Http\Requests\WishlistCategoryUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class WishlistCategoryController extends Controller
{
    /**
     * Display a listing of the wishlist categories.
     */
    public function index(Request $request)
    {
        try {
            $query = WishlistCategory::with(['user', 'items.product']);

            // Filter by user if specified
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Filter by default status if specified
            if ($request->has('is_default')) {
                $query->where('is_default', $request->boolean('is_default'));
            }

            $wishlistCategories = $query->orderBy('created_at', 'desc')->paginate(15);

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
            $wishlistCategory = WishlistCategory::with(['user', 'items.product'])->findOrFail($id);

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
            DB::beginTransaction();

            $data = $request->validated();
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
            $wishlistCategory = WishlistCategory::findOrFail($id);

            DB::beginTransaction();

            $data = $request->validated();
            $wishlistCategory->update($data);

            // Handle file uploads
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
            $wishlistCategory = WishlistCategory::findOrFail($id);

            // Check if it's a default category
            if ($wishlistCategory->isDefault()) {
                return response()->json([
                    'message' => 'Default wishlist categories cannot be deleted.',
                    'data' => null,
                    'errors' => ['wishlist_category' => ['Default wishlist categories are protected.']],
                    'code' => 403,
                ], 403);
            }

            // Check if category has items
            if ($wishlistCategory->items()->count() > 0) {
                return response()->json([
                    'message' => 'Cannot delete wishlist category with items.',
                    'data' => null,
                    'errors' => ['wishlist_category' => ['Please remove all items first.']],
                    'code' => 400,
                ], 400);
            }

            DB::beginTransaction();

            $wishlistCategory->delete();
            $wishlistCategory->setIcon(null);
            $wishlistCategory->setImage(null);

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
     * Get user's wishlist categories.
     */
    public function getUserWishlistCategories($userId)
    {
        try {
            $wishlistCategories = WishlistCategory::with(['user', 'items.product'])
                ->where('user_id', $userId)
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'message' => 'User wishlist categories retrieved successfully.',
                'data' => WishlistCategoryResource::collection($wishlistCategories),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve user wishlist categories.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }
}
