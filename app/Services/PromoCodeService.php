<?php

namespace App\Services;

use App\Models\PromoCode;
use App\Models\PromoCodeUsage;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PromoCodeService
{
    /**
     * Create a new promo code
     *
     * @param array $data
     * @return PromoCode
     */
    public function createPromoCode(array $data): PromoCode
    {
        // Generate a random code if not provided
        if (empty($data['code'])) {
            $data['code'] = $this->generateUniqueCode();
        } else {
            $data['code'] = strtoupper($data['code']);
        }

        // Set default values
        $data['is_active'] = $data['is_active'] ?? true;
        $data['total_usage_count'] = 0;

        $promoCode = DB::transaction(function () use ($data) {
            // Create the promo code
            $promoCode = PromoCode::create($data);

            // Attach related products if any
            if (isset($data['product_ids']) && is_array($data['product_ids'])) {
                $promoCode->products()->sync($data['product_ids']);
            }

            // Attach related categories if any
            if (isset($data['category_ids']) && is_array($data['category_ids'])) {
                $promoCode->categories()->sync($data['category_ids']);
            }

            return $promoCode;
        });

        return $promoCode;
    }

    /**
     * Update an existing promo code
     *
     * @param PromoCode $promoCode
     * @param array $data
     * @return PromoCode
     */
    public function updatePromoCode(PromoCode $promoCode, array $data): PromoCode
    {
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        $promoCode->update($data);

        if (isset($data['product_ids']) && is_array($data['product_ids'])) {
            $promoCode->products()->sync($data['product_ids']);
        }

        if (isset($data['category_ids']) && is_array($data['category_ids'])) {
            $promoCode->categories()->sync($data['category_ids']);
        }

        return $promoCode->fresh();
    }

    /**
     * Validate and apply promo code to an order
     *
     * @param string $code
     * @param User $user
     * @param array $cartItems Array of cart items with product_id and quantity
     * @return array
     * @throws ValidationException
     */
    public function validateAndApplyCode(string $code, User $user, array $cartItems = []): array
    {
        $promoCode = PromoCode::where('code', strtoupper($code))->first();

        if (!$promoCode) {
            throw ValidationException::withMessages([
                'promo_code' => ['The provided promo code is invalid.'],
            ]);
        }

        // Check if promo code is active
        if (!$promoCode->is_active) {
            throw ValidationException::withMessages([
                'promo_code' => ['This promo code is no longer active.'],
            ]);
        }

        // Check validity period
        $now = now();
        if ($promoCode->start_date && $promoCode->start_date->gt($now)) {
            throw ValidationException::withMessages([
                'promo_code' => ['This promo code is not yet valid.'],
            ]);
        }

        if ($promoCode->end_date && $promoCode->end_date->lt($now)) {
            throw ValidationException::withMessages([
                'promo_code' => ['This promo code has expired.'],
            ]);
        }

        // Check total usage limit
        if ($promoCode->total_usage_limit !== null && 
            $promoCode->total_usage_count >= $promoCode->total_usage_limit) {
            throw ValidationException::withMessages([
                'promo_code' => ['This promo code has reached its maximum usage limit.'],
            ]);
        }

        // Check per user usage limit
        if ($promoCode->per_user_usage_limit !== null && $user) {
            $userUsageCount = $promoCode->usages()
                ->where('user_id', $user->id)
                ->count();

            if ($userUsageCount >= $promoCode->per_user_usage_limit) {
                throw ValidationException::withMessages([
                    'promo_code' => ['You have reached the maximum usage limit for this promo code.'],
                ]);
            }
        }

        // Check if products in cart are eligible for this promo
        if ($promoCode->target_type === 'products' && !empty($cartItems)) {
            $eligibleProductIds = $promoCode->products->pluck('id')->toArray();
            $cartProductIds = collect($cartItems)->pluck('product_id')->toArray();
            
            if (empty(array_intersect($eligibleProductIds, $cartProductIds))) {
                throw ValidationException::withMessages([
                    'promo_code' => ['This promo code is not applicable to any products in your cart.'],
                ]);
            }
        }

        // If we get here, the promo code is valid
        return [
            'promo_code' => $promoCode,
            'discount_amount' => 0, // This would be calculated based on order total
            'is_valid' => true,
        ];
    }

    /**
     * Record promo code usage
     *
     * @param PromoCode $promoCode
     * @param User $user
     * @param Order $order
     * @param float $discountAmount
     * @return PromoCodeUsage
     */
    public function recordUsage(PromoCode $promoCode, User $user, Order $order, float $discountAmount): PromoCodeUsage
    {
        $usage = new PromoCodeUsage([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'discount_amount' => $discountAmount,
            'applied_at' => now(),
        ]);

        $promoCode->usages()->save($usage);
        $promoCode->increment('total_usage_count');

        return $usage;
    }

    /**
     * Generate a unique promo code
     *
     * @param int $length
     * @return string
     */
    protected function generateUniqueCode(int $length = 8): string
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (PromoCode::where('code', $code)->exists());

        return $code;
    }

    /**
     * Calculate discount amount for an order
     *
     * @param PromoCode $promoCode
     * @param float $orderTotal
     * @param array $cartItems
     * @return float
     */
    public function calculateDiscount(PromoCode $promoCode, float $orderTotal, array $cartItems = []): float
    {
        $discountAmount = 0;
        
        if ($promoCode->discount_type === 'percentage') {
            $discountAmount = $orderTotal * ($promoCode->discount_value / 100);
        } else {
            $discountAmount = min($promoCode->discount_value, $orderTotal);
        }

        // If promo code is for specific products, calculate discount only for those products
        if ($promoCode->target_type === 'products' && !empty($cartItems)) {
            $eligibleProductIds = $promoCode->products->pluck('id')->toArray();
            $eligibleItems = array_filter($cartItems, function($item) use ($eligibleProductIds) {
                return in_array($item['product_id'], $eligibleProductIds);
            });
            
            $eligibleTotal = array_reduce($eligibleItems, function($carry, $item) {
                return $carry + ($item['price'] * $item['quantity']);
            }, 0);
            
            if ($promoCode->discount_type === 'percentage') {
                $discountAmount = $eligibleTotal * ($promoCode->discount_value / 100);
            } else {
                $discountAmount = min($promoCode->discount_value, $eligibleTotal);
            }
        }

        return round($discountAmount, 2);
    }

    /**
     * Get promo code usage statistics
     *
     * @param PromoCode $promoCode
     * @return array
     */
    public function getUsageStatistics(PromoCode $promoCode): array
    {
        return [
            'total_usage' => $promoCode->total_usage_count,
            'remaining_usage' => $promoCode->total_usage_limit ? 
                $promoCode->total_usage_limit - $promoCode->total_usage_count : null,
            'usage_percentage' => $promoCode->total_usage_limit ? 
                ($promoCode->total_usage_count / $promoCode->total_usage_limit) * 100 : null,
            'recent_usages' => $promoCode->usages()
                ->with(['user', 'order'])
                ->latest()
                ->take(10)
                ->get(),
        ];
    }
}
