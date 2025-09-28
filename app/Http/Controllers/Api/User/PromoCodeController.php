<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\PromoCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// log 
use Illuminate\Support\Facades\Log;

class PromoCodeController extends Controller
{
    /**
     * Validate a promo code and return its details.
     *
     * @param string $code
     * @return JsonResponse
     */
    public function show(string $code): JsonResponse
    {
        $promoCode = PromoCode::where('code', strtoupper($code))->first();

        if (!$promoCode) {
            return response()->json(['message' => 'Invalid promo code.'], 404);
        }

        // Check if the promo code is active and valid
        if (
            !$promoCode->is_active ||
            ($promoCode->start_date && $promoCode->start_date->isFuture()) ||
            ($promoCode->end_date && $promoCode->end_date->isPast()) ||
            ($promoCode->usage_limit && $promoCode->usage_count >= $promoCode->usage_limit)
        ) {
            return response()->json(['message' => 'This promo code is expired or invalid.'], 422);
        }

        // Return public-safe data
        return response()->json([
            'message' => 'Promo code is valid.',
            'data' => [
                'code' => $promoCode->code,
                'type' => $promoCode->type,
                'value' => $promoCode->value,
                'product_id' => $promoCode->product_id,
            ]
        ]);
    }

    /**
     * Apply a promo code to the user's cart.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function apply(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string']);

        $user = Auth::user();

        // Debug: log request
        Log::info('Request promo code apply', [
            'input_code' => $request->code,
            'upper_code' => strtoupper($request->code),
            'user_id'    => $user->id,
        ]);

        // Try to fetch promo code
        $promoCode = PromoCode::where('code', strtoupper($request->code))->first();

        // Debug: log promo code record
        Log::info('Promo code record from DB', [
            'promoCode' => $promoCode ? $promoCode->toArray() : null
        ]);

        // Fetch cart items
        $cartItems = Cart::with('productDetail.product')
            ->where('user_id', $user->id)
            ->get();

        // Debug: log cart items
        Log::info('Cart items for user', [
            'count' => $cartItems->count(),
            'items' => $cartItems->toArray(),
        ]);

        // 1. Basic validation
        if (!$promoCode) {
            return response()->json([
                'message' => 'Invalid promo code.',
                'debug'   => [
                    'searched_code' => strtoupper($request->code),
                    'found'         => false
                ]
            ], 404);
        }
        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'Your cart is empty.',
                'debug'   => ['cart_count' => 0]
            ], 422);
        }

        // 2. Check if promo code is valid/active
        if (
            !$promoCode->is_active ||
            ($promoCode->start_date && $promoCode->start_date->isFuture()) ||
            ($promoCode->end_date && $promoCode->end_date->isPast()) ||
            ($promoCode->usage_limit && $promoCode->usage_count >= $promoCode->usage_limit)
        ) {
            return response()->json([
                'message' => 'This promo code is expired or invalid.',
                'debug'   => [
                    'is_active'   => $promoCode->is_active,
                    'start_date'  => $promoCode->start_date,
                    'end_date'    => $promoCode->end_date,
                    'usage_limit' => $promoCode->usage_limit,
                    'usage_count' => $promoCode->usage_count,
                ]
            ], 422);
        }

        // 3. Check applicability to cart
        $applicableItem = $cartItems->firstWhere('productDetail.product.id', $promoCode->product_id);

        if (!$applicableItem) {
            return response()->json([
                'message' => 'This code is not valid for any items in your cart.',
                'debug'   => [
                    'promo_product_id' => $promoCode->product_id,
                    'cart_products'    => $cartItems->pluck('productDetail.product.id')
                ]
            ], 422);
        }

        // 4. Calculate discount
        $discountValue = 0;
        $itemPrice = $applicableItem->productDetail->final_price;
        $itemQuantity = $applicableItem->quantity;

        if ($promoCode->type === 'percentage') {
            $discountValue = ($itemPrice * $itemQuantity) * ($promoCode->value / 100);
        } elseif ($promoCode->type === 'fixed') {
            $discountValue = min($promoCode->value, $itemPrice * $itemQuantity);
        }

        // Debug: log discount calculation
        Log::info('Discount calculation', [
            'promo_type'    => $promoCode->type,
            'promo_value'   => $promoCode->value,
            'item_price'    => $itemPrice,
            'item_quantity' => $itemQuantity,
            'discountValue' => $discountValue,
        ]);

        // 5. Save promo to session
        session([
            'promo_code' => $promoCode->code,
            'discount'   => round($discountValue, 2)
        ]);

        return response()->json([
            'message'    => 'Promo code applied successfully!',
            'discount'   => session('discount'),
            'promo_code' => session('promo_code'),
        ]);
    }

    /**
     * Remove the promo code from the user's session.
     *
     * @return JsonResponse
     */
    public function remove(): JsonResponse
    {
        session()->forget(['promo_code', 'discount']);
        return response()->json(['message' => 'Promo code removed.']);
    }
}

