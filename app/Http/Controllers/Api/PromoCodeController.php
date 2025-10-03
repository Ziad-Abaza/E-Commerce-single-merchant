<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PromoCodeController extends Controller
{
  /**
   * Validate a promo code and return discount info.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function validate(Request $request)
  {
    $request->validate([
      'code' => 'required|string',
      'subtotal' => 'required|numeric|min:0',
      'shipping' => 'nullable|numeric|min:0',
    ]);

    $user = Auth::user();
    $code = $request->input('code');
    $subtotal = $request->input('subtotal');
    $shipping = $request->input('shipping', 0);

    $promoCode = PromoCode::where('code', $code)->first();

    if (!$promoCode) {
      return response()->json([
        'success' => false,
        'message' => 'Invalid promo code.',
        'code' => 404,
      ], 404);
    }

    if (!$promoCode->isValid($user)) {
      return response()->json([
        'success' => false,
        'message' => 'This promo code is not valid at this time.',
        'code' => 400,
      ], 400);
    }

    // Calculate discounted amount for the target type
    $discountedAmount = $promoCode->applyDiscount($subtotal, $shipping);

    // Determine original target amount
    switch ($promoCode->target_type) {
      case 'shipping':
        $originalAmount = $shipping;
        break;
      case 'order':
        $originalAmount = $subtotal + $shipping;
        break;
      default:
        $originalAmount = $subtotal;
        break;
    }

    $discountAmount = max(0, $originalAmount - $discountedAmount);

    return response()->json([
      'success' => true,
      'message' => 'Promo code is valid.',
      'data' => [
        'id' => $promoCode->id,
        'code' => $promoCode->code,
        'name' => $promoCode->name,
        'discount_type' => $promoCode->discount_type,
        'discount_value' => $promoCode->discount_value,
        'target_type' => $promoCode->target_type,
        'discount_amount' => round($discountAmount, 2),
        'original_amount' => round($originalAmount, 2),
        'final_amount' => round($discountedAmount, 2),
      ],
      'code' => 200,
    ]);
  }
}
