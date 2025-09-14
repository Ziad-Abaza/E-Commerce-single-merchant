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
                    // Add calculated fields
                    $item->total_price = $item->productDetail->final_price * $item->quantity;
                    return $item;
                });

            $summary = [
                'total_items' => $cartItems->sum('quantity'),
                'subtotal' => $cartItems->sum('total_price'),
                'total' => $cartItems->sum('total_price'),
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
    public function store(CartStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $userId = Auth::id() ?? $data['user_id'];

            DB::beginTransaction();

            // Check if item already exists in cart
            $existingCartItem = Cart::where('user_id', $userId)
                ->where('product_detail_id', $data['product_detail_id'])
                ->first();

            if ($existingCartItem) {
                // Update quantity
                $existingCartItem->quantity += $data['quantity'];
                $existingCartItem->save();
                $cartItem = $existingCartItem;
            } else {
                // Create new cart item
                $cartItem = Cart::create([
                    'user_id' => $userId,
                    'product_detail_id' => $data['product_detail_id'],
                    'quantity' => $data['quantity'],
                ]);
            }

            // Load relationships
            $cartItem->load('productDetail.product');

            // Calculate total price
            $cartItem->total_price = $cartItem->productDetail->final_price * $cartItem->quantity;

            DB::commit();

            return response()->json([
                'message' => 'Cart item added/updated successfully.',
                'data' => new CartResource($cartItem),
                'errors' => null,
                'code' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to add item to cart.',
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

            // Verify ownership
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
                // Remove item if quantity is 0 or less
                $cartItem->delete();
                DB::commit();

                return response()->json([
                    'message' => 'Cart item removed successfully.',
                    'data' => null,
                    'errors' => null,
                    'code' => 200,
                ], 200);
            } else {
                // Update quantity
                $cartItem->quantity = $data['quantity'];
                $cartItem->save();

                // Load relationships
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

            // Verify ownership
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
