<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WishlistItem;
use App\Http\Resources\WishlistItemResource;
use App\Http\Requests\WishlistItemStoreRequest;
use App\Http\Requests\WishlistItemUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class WishlistItemController extends Controller
{
    /**
     * Display a listing of the wishlist items.
     */
    public function index(Request $request)
    {
        try {
            $query = WishlistItem::with(['category.user', 'product']);

            // Filter by wishlist category if specified
            if ($request->has('wishlist_category_id')) {
                $query->where('wishlist_category_id', $request->wishlist_category_id);
            }

            // Filter by product if specified
            if ($request->has('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            // Filter by user through wishlist category
            if ($request->has('user_id')) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('user_id', $request->user_id);
                });
            }

            $wishlistItems = $query->orderBy('created_at', 'desc')->paginate(15);

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
            $wishlistItem = WishlistItem::with(['category.user', 'product'])->findOrFail($id);

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
            DB::beginTransaction();

            $data = $request->validated();

            // Check if the product is already in the wishlist category
            $existingItem = WishlistItem::where('wishlist_category_id', $data['wishlist_category_id'])
                ->where('product_id', $data['product_id'])
                ->first();

            if ($existingItem) {
                return response()->json([
                    'message' => 'Product is already in this wishlist category.',
                    'data' => null,
                    'errors' => ['wishlist_item' => ['Product already exists in this wishlist category.']],
                    'code' => 409,
                ], 409);
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
            $wishlistItem = WishlistItem::findOrFail($id);

            DB::beginTransaction();

            $data = $request->validated();

            // Check if the product is already in another wishlist category
            if (isset($data['wishlist_category_id']) && isset($data['product_id'])) {
                $existingItem = WishlistItem::where('wishlist_category_id', $data['wishlist_category_id'])
                    ->where('product_id', $data['product_id'])
                    ->where('id', '!=', $id)
                    ->first();

                if ($existingItem) {
                    return response()->json([
                        'message' => 'Product is already in this wishlist category.',
                        'data' => null,
                        'errors' => ['wishlist_item' => ['Product already exists in this wishlist category.']],
                        'code' => 409,
                    ], 409);
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
            $wishlistItem = WishlistItem::findOrFail($id);
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
    public function moveToCategory(Request $request, $id)
    {
        try {
            $wishlistItem = WishlistItem::findOrFail($id);

            $request->validate([
                'wishlist_category_id' => 'required|exists:wishlist_categories,id',
            ]);

            $newCategoryId = $request->wishlist_category_id;

            // Check if the product is already in the target category
            $existingItem = WishlistItem::where('wishlist_category_id', $newCategoryId)
                ->where('product_id', $wishlistItem->product_id)
                ->where('id', '!=', $id)
                ->first();

            if ($existingItem) {
                return response()->json([
                    'message' => 'Product is already in the target wishlist category.',
                    'data' => null,
                    'errors' => ['wishlist_item' => ['Product already exists in the target wishlist category.']],
                    'code' => 409,
                ], 409);
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
     * Get user's wishlist items.
     */
    public function getUserWishlistItems($userId)
    {
        try {
            $wishlistItems = WishlistItem::with(['category.user', 'product'])
                ->whereHas('category', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'message' => 'User wishlist items retrieved successfully.',
                'data' => WishlistItemResource::collection($wishlistItems),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve user wishlist items.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }
}
