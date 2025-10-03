<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Http\Resources\CartResource;
use App\Http\Requests\CartStoreRequest;
use App\Http\Requests\CartUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Get current user's cart with all items
     */
    public function index()
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json([
                    'message' => 'User not authenticated',
                    'data' => [],
                    'errors' => ['auth' => ['User must be logged in']],
                    'code' => 401,
                ], 401);
            }

            $cartItems = Cart::with(['productDetail.product'])
                ->where('user_id', $userId)
                ->get()
                ->filter(function ($item) {
                    return $item->productDetail &&
                        $item->productDetail->product &&
                        !$item->productDetail->product->deleted_at;
                })
                ->map(function ($item) {
                    $item->total_price = $item->productDetail->final_price * $item->quantity;
                    return $item;
                });

            $subtotal = $cartItems->sum('total_price');
            
            $summary = [
                'total_items' => $cartItems->sum('quantity'),
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ];

            return response()->json([
                'message' => 'Cart retrieved successfully.',
                'data' => [
                    'items' => CartResource::collection($cartItems),
                    'summary' => $summary
                ],
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve cart.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Add item to cart or update quantity if exists
     */
    /**
     * Add item to cart or update quantity if exists
     */
    public function store(CartStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $userId = Auth::id();

            if (!$userId && isset($data['user_id'])) {
                $userId = $data['user_id'];
            }

            if (!$userId) {
                return response()->json(['message' => 'User not authenticated', 'code' => 401], 401);
            }

            // Validate that the product detail exists and is active
            $productDetail = \App\Models\ProductDetail::with('product')->find($data['product_detail_id']);

            if (!$productDetail || !$productDetail->product || $productDetail->product->is_active == false) {
                return response()->json([
                    'message' => 'The selected product is no longer available.',
                    'errors' => ['product' => ['Product not found or has been removed.']]
                ], 404);
            }

            DB::beginTransaction();

            // Find the item or create a new instance
            $cartItem = Cart::firstOrNew([
                'user_id' => $userId,
                'product_detail_id' => $data['product_detail_id']
            ]);

            // Add the new quantity to the existing quantity (if it's a new item, quantity will be 0)
            $cartItem->quantity = ($cartItem->quantity ?? 0) + $data['quantity'];

            // Save the item with ONLY the columns that exist in your database table.
            $cartItem->save();

            DB::commit();

            // Load relations for the JSON response
            $cartItem->load('productDetail.product');

            return response()->json([
                'message' => 'Cart item added/updated successfully.',
                'data' => new CartResource($cartItem),
                'errors' => null,
                'code' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to add item to cart', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to add item to cart.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Sync multiple cart items at once
     * Expects: { items: [{ product_detail_id: 1, quantity: 2 }, ...] }
     */
    public function sync(Request $request)
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'message' => 'User not authenticated',
                'data' => null,
                'errors' => ['auth' => ['User must be logged in']],
                'code' => 401,
            ], 401);
        }

        $items = $request->input('items', []);

        if (empty($items) || !is_array($items)) {
            return response()->json([
                'message' => 'No items provided for sync',
                'data' => null,
                'errors' => ['items' => ['Please provide an array of cart items']],
                'code' => 400,
            ], 400);
        }

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                $productDetailId = $item['product_detail_id'] ?? null;
                $quantity = max(0, intval($item['quantity'] ?? 0));

                if (!$productDetailId || $quantity <= 0) continue;

                $cartItem = Cart::firstOrNew([
                    'user_id' => $userId,
                    'product_detail_id' => $productDetailId,
                ]);

                // إذا موجود مسبقًا، نجمع الكمية القديمة مع الجديدة
                $cartItem->quantity = ($cartItem->quantity ?? 0) + $quantity;
                $cartItem->save();
            }

            DB::commit();

            // إعادة تحميل الكارت بعد المزامنة
            $cartItems = Cart::with('productDetail.product')->where('user_id', $userId)->get();

            $summary = [
                'total_items' => $cartItems->sum('quantity'),
                'subtotal' => $cartItems->sum(function ($item) {
                    return $item->productDetail->final_price * $item->quantity;
                }),
            ];

            return response()->json([
                'message' => 'Cart synced successfully.',
                'data' => [
                    'items' => CartResource::collection($cartItems),
                    'summary' => $summary,
                ],
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to sync cart', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to sync cart.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(CartUpdateRequest $request, $id)
    {
        try {
            $cartItem = Cart::findOrFail($id);

            if (Auth::id() && $cartItem->user_id !== Auth::id()) {
                return response()->json([
                    'message' => 'Unauthorized access',
                    'data' => null,
                    'errors' => ['auth' => ['You can only modify your own cart items']],
                    'code' => 403,
                ], 403);
            }

            $data = $request->validated();
            DB::beginTransaction();

            if ($data['quantity'] <= 0) {
                $cartItem->delete();
                DB::commit();

                return response()->json([
                    'message' => 'Cart item removed successfully.',
                    'data' => null,
                    'errors' => null,
                    'code' => 200,
                ], 200);
            } else {
                $cartItem->quantity = $data['quantity'];
                $cartItem->save();
                $cartItem->load('productDetail.product');
                $cartItem->total_price = $cartItem->productDetail->final_price * $cartItem->quantity;
                DB::commit();

                return response()->json([
                    'message' => 'Cart item updated successfully.',
                    'data' => new CartResource($cartItem),
                    'errors' => null,
                    'code' => 200,
                ], 200);
            }
        } catch (ModelNotFoundException $e) {
            Log::info('Cart item not found', ['id' => $id]);
            return response()->json([
                'message' => 'Cart item not found.',
                'data' => null,
                'errors' => ['cart' => ['Cart item could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('Failed to update cart item', ['id' => $id]);
            return response()->json([
                'message' => 'Failed to update cart item.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function destroy($id)
    {
        try {
            $cartItem = Cart::findOrFail($id);

            if (Auth::id() && $cartItem->user_id !== Auth::id()) {
                return response()->json([
                    'message' => 'Unauthorized access',
                    'data' => null,
                    'errors' => ['auth' => ['You can only remove your own cart items']],
                    'code' => 403,
                ], 403);
            }

            $cartItem->delete();

            return response()->json([
                'message' => 'Cart item removed successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Cart item not found.',
                'data' => null,
                'errors' => ['cart' => ['Cart item could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to remove cart item.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Clear all items from cart
     */
    public function clear()
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

            Cart::where('user_id', $userId)->delete();


            return response()->json([
                'message' => 'Cart cleared successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to clear cart.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }
}
