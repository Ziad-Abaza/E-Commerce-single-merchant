<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\WishlistItem;
use App\Http\Resources\WishlistItemResource;
use App\Http\Requests\WishlistItemStoreRequest;
use App\Http\Requests\WishlistItemUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WishlistItemController extends Controller
{
    /**
     * Display a listing of the wishlist items.
     */
    public function index(Request $request)
    {
        try {
            // Always filter by authenticated user unless admin
            $userId = Auth::id();

            if (!$userId) {
                return response()->json([
                    'message' => 'User not authenticated',
                    'data' => null,
                    'errors' => ['auth' => ['User must be logged in']],
                    'code' => 401,
                ], 401);
            }

            $query = WishlistItem::with(['category.user', 'product'])
                ->whereHas('product') // Add this line
                ->whereHas('category', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                });

            // Filter by wishlist category if specified
            if ($request->has('wishlist_category_id')) {
                $query->where('wishlist_category_id', $request->wishlist_category_id);
            }

            // Filter by product if specified
            if ($request->has('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            $wishlistItems = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

            return response()->json([
                'message' => 'Wishlist items retrieved successfully.',
                'data' => WishlistItemResource::collection($wishlistItems),
                'pagination' => [
                    'current_page' => $wishlistItems->currentPage(),
                    'last_page' => $wishlistItems->lastPage(),
                    'per_page' => $wishlistItems->perPage(),
                    'total' => $wishlistItems->total(),
                ],
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve wishlist items.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Display the specified wishlist item.
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

            $wishlistItem = WishlistItem::with(['category.user', 'product'])
                ->whereHas('category', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->findOrFail($id);

            return response()->json([
                'message' => 'Wishlist item retrieved successfully.',
                'data' => new WishlistItemResource($wishlistItem),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Wishlist item not found.',
                'data' => null,
                'errors' => ['wishlist_item' => ['Wishlist item could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve wishlist item.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Store a newly created wishlist item.
     */
    public function store(WishlistItemStoreRequest $request)
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

            // Verify category belongs to user
            $category = \App\Models\WishlistCategory::where('id', $data['wishlist_category_id'])
                ->where('user_id', $userId)
                ->first();

            if (!$category) {
                return response()->json([
                    'message' => 'Invalid wishlist category',
                    'data' => null,
                    'errors' => ['wishlist_category' => ['Category does not belong to you']],
                    'code' => 403,
                ], 403);
            }

            // Check if the product is already in the wishlist category
            $existingItem = WishlistItem::where('wishlist_category_id', $data['wishlist_category_id'])
                ->where('product_id', $data['product_id'])
                ->first();

            if ($existingItem) {
                return response()->json([
                    'message' => 'Product is already in this wishlist category.',
                    'data' => new WishlistItemResource($existingItem->load(['category.user', 'product'])),
                    'errors' => null,
                    'code' => 200,
                ], 200);
            }

            $wishlistItem = WishlistItem::create($data);

            DB::commit();

            return response()->json([
                'message' => 'Wishlist item created successfully.',
                'data' => new WishlistItemResource($wishlistItem->load(['category.user', 'product'])),
                'errors' => null,
                'code' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create wishlist item.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Update the specified wishlist item.
     */
    public function update(WishlistItemUpdateRequest $request, $id)
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

            $wishlistItem = WishlistItem::whereHas('category', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->findOrFail($id);

            DB::beginTransaction();

            $data = $request->validated();

            // If changing category, verify it belongs to user
            if (isset($data['wishlist_category_id'])) {
                $category = \App\Models\WishlistCategory::where('id', $data['wishlist_category_id'])
                    ->where('user_id', $userId)
                    ->first();

                if (!$category) {
                    return response()->json([
                        'message' => 'Invalid wishlist category',
                        'data' => null,
                        'errors' => ['wishlist_category' => ['Category does not belong to you']],
                        'code' => 403,
                    ], 403);
                }
            }

            // Check if the product is already in another wishlist category
            if (isset($data['wishlist_category_id']) && isset($data['product_id'])) {
                $existingItem = WishlistItem::where('wishlist_category_id', $data['wishlist_category_id'])
                    ->where('product_id', $data['product_id'])
                    ->where('id', '!=', $id)
                    ->first();

                if ($existingItem) {
                    return response()->json([
                        'message' => 'Product is already in this wishlist category.',
                        'data' => new WishlistItemResource($existingItem->load(['category.user', 'product'])),
                        'errors' => null,
                        'code' => 200,
                    ], 200);
                }
            }

            $wishlistItem->update($data);

            DB::commit();

            return response()->json([
                'message' => 'Wishlist item updated successfully.',
                'data' => new WishlistItemResource($wishlistItem->fresh(['category.user', 'product'])),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Wishlist item not found.',
                'data' => null,
                'errors' => ['wishlist_item' => ['Wishlist item could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update wishlist item.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Remove the specified wishlist item.
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

            $wishlistItem = WishlistItem::whereHas('category', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->findOrFail($id);

            $wishlistItem->delete();

            return response()->json([
                'message' => 'Wishlist item deleted successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Wishlist item not found.',
                'data' => null,
                'errors' => ['wishlist_item' => ['Wishlist item could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete wishlist item.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Move wishlist item to another category.
     */
    public function move(Request $request, $id)
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

            $wishlistItem = WishlistItem::whereHas('category', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->findOrFail($id);

            $request->validate([
                'wishlist_category_id' => 'required|exists:wishlist_categories,id',
            ]);

            $newCategoryId = $request->wishlist_category_id;

            // Verify new category belongs to user
            $newCategory = \App\Models\WishlistCategory::where('id', $newCategoryId)
                ->where('user_id', $userId)
                ->first();

            if (!$newCategory) {
                return response()->json([
                    'message' => 'Invalid wishlist category',
                    'data' => null,
                    'errors' => ['wishlist_category' => ['Category does not belong to you']],
                    'code' => 403,
                ], 403);
            }

            // Check if the product is already in the target category
            $existingItem = WishlistItem::where('wishlist_category_id', $newCategoryId)
                ->where('product_id', $wishlistItem->product_id)
                ->where('id', '!=', $id)
                ->first();

            if ($existingItem) {
                return response()->json([
                    'message' => 'Product is already in the target wishlist category.',
                    'data' => new WishlistItemResource($existingItem->load(['category.user', 'product'])),
                    'errors' => null,
                    'code' => 200,
                ], 200);
            }

            DB::beginTransaction();

            $wishlistItem->update(['wishlist_category_id' => $newCategoryId]);

            DB::commit();

            return response()->json([
                'message' => 'Wishlist item moved successfully.',
                'data' => new WishlistItemResource($wishlistItem->fresh(['category.user', 'product'])),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Wishlist item not found.',
                'data' => null,
                'errors' => ['wishlist_item' => ['Wishlist item could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to move wishlist item.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Check if product is in wishlist
     */
    public function checkProductInWishlist(Request $request)
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

            $request->validate([
                'product_id' => 'required|exists:products,id',
            ]);

            $wishlistItem = WishlistItem::where('product_id', $request->product_id)
                ->whereHas('category', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->first();

            return response()->json([
                'message' => 'Product wishlist status retrieved successfully.',
                'data' => [
                    'in_wishlist' => $wishlistItem ? true : false,
                    'wishlist_item' => $wishlistItem ? new WishlistItemResource($wishlistItem->load(['category.user', 'product'])) : null
                ],
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to check product wishlist status.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }
}
