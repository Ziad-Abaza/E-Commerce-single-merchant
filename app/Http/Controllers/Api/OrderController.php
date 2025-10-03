<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PromoCode;
use App\Models\Setting;
use App\Models\ProductDetail;
use App\Http\Resources\OrderResource;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Events\NewOrderEvent;
use App\Notifications\owner\NewOrderNotification;
use Illuminate\Support\Str;
use App\Events\OrderCancelledEvent;
use App\Notifications\owner\OrderCancelledNotification;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'message' => 'You are not logged in, please log in first',
                    'data' => null,
                    'errors' => null,
                    'code' => 401,
                    'success' => false
                ], 401);
            }

            $query = Order::with(['user', 'items.product'])
                ->where('user_id', $user->id);

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $orders = $query->orderBy('created_at', 'desc')->paginate(15);

            return response()->json([
                'message' => 'Orders retrieved successfully.',
                'data' => OrderResource::collection($orders),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                ],
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve orders.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $order = Order::with(['user', 'items.product', 'promoCode'])
                ->where('user_id', Auth::id())
                ->findOrFail($id);

            return response()->json([
                'message' => 'Order retrieved successfully.',
                'data' => new OrderResource($order),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Order not found.',
                'data' => null,
                'errors' => ['order' => ['Order could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve order.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    public function store(OrderStoreRequest $request)
    {

        try {
            DB::beginTransaction();
            $validated = $request->validated();

            Log::info('Order Store Request: ', $validated);

            // First, get base order amounts without tax
            $orderData = $this->calculateOrderAmounts($request->items, $this->getOrderSettings(), false);
            
            // Initialize order data with base amounts
            $data = [
                'order_number' => $this->generateOrderNumber(),
                'user_id' => Auth::id(),
                'phone' => Auth::user()->phone ?? null,
                'status' => 'pending',
                'shipping_address' => Auth::user()->address ?? null,
                'notes' => $validated['notes'] ?? null,
                'subtotal' => $orderData['subtotal'],
                'shipping_cost' => $orderData['shipping_cost'],
                'tax_amount' => 0, // Will be calculated after applying promo code
                'total_amount' => 0, // Will be calculated after applying promo code and tax
                'currency' => $orderData['currency'],
                'discount_amount' => 0,
                'promo_code_id' => null,
            ];

            $discountAmount = 0;
            $promoCode = null;
            $amountBeforeTax = $orderData['subtotal'] + $orderData['shipping_cost'];

            // Apply promo code if provided and valid
            if (!empty($validated['promo_code_id'])) {
                $promoCode = PromoCode::find($validated['promo_code_id']);
                $user = Auth::user();

                if ($promoCode && $promoCode->isValid($user)) {
                    // Calculate discount based on target type
                    $discountedAmount = $promoCode->applyDiscount(
                        $orderData['subtotal'],
                        $orderData['shipping_cost']
                    );

                    // Determine original amount based on target_type
                    switch ($promoCode->target_type) {
                        case 'shipping':
                            $originalAmount = $orderData['shipping_cost'];
                            $discountAmount = max(0, $originalAmount - $discountedAmount);
                            // Only reduce shipping cost, not subtotal
                            $data['shipping_cost'] = max(0, $data['shipping_cost'] - $discountAmount);
                            break;
                        case 'order':
                            $originalAmount = $orderData['subtotal'] + $orderData['shipping_cost'];
                            $discountAmount = max(0, $originalAmount - $discountedAmount);
                            // Apply discount proportionally to subtotal and shipping
                            $subtotalRatio = $orderData['subtotal'] / max(1, $originalAmount);
                            $shippingRatio = $orderData['shipping_cost'] / max(1, $originalAmount);
                            $data['subtotal'] = max(0, $orderData['subtotal'] - ($discountAmount * $subtotalRatio));
                            $data['shipping_cost'] = max(0, $orderData['shipping_cost'] - ($discountAmount * $shippingRatio));
                            break;
                        default: // 'products' or any other type
                            $originalAmount = $orderData['subtotal'];
                            $discountAmount = max(0, $originalAmount - $discountedAmount);
                            // Only reduce subtotal, not shipping
                            $data['subtotal'] = max(0, $orderData['subtotal'] - $discountAmount);
                            break;
                    }

                    $discountAmount = round($discountAmount, 2);
                    $data['discount_amount'] = $discountAmount;
                    $data['promo_code_id'] = $promoCode->id;
                }
            }
            
            // Calculate tax after applying promo code discount
            $settings = $this->getOrderSettings();
            $taxRate = is_numeric($settings['tax_rate']) ? (float)$settings['tax_rate'] : 0;
            $data['tax_amount'] = $data['subtotal'] * $taxRate;
            
            // Calculate final total amount
            $data['total_amount'] = $data['subtotal'] + $data['shipping_cost'] + $data['tax_amount'];

            $order = Order::create($data);

            // Save order items with calculated prices
            if (!empty($orderData['items'])) {
                $order->items()->createMany($orderData['items']);
            }

            // Handle file uploads
            try {
                if ($request->hasFile('receipt')) {
                    $order->setReceipt($request->file('receipt'));
                }

                if ($request->hasFile('invoice')) {
                    $order->setInvoice($request->file('invoice'));
                }

                if ($request->hasFile('attachments')) {
                    $order->setAttachment($request->file('attachments'));
                }
            } catch (\Exception $fileException) {
                throw $fileException;
            }

            DB::commit();

            // Increment usage if promo code was applied
            if ($promoCode) {
                $promoCode->incrementUsage(Auth::user());
            }
            
            $owners = \App\Models\User::role('owner')->get();

            foreach ($owners as $owner) {
                try {
                    $owner->notify(new NewOrderNotification($order));
                } catch (\Exception $notificationException) {
                    // Continue with other owners even if one fails
                }
            }

            NewOrderEvent::dispatch($order);

            return response()->json([
                'message' => 'Order created successfully.',
                'data' => new OrderResource($order->load(['user', 'items.product'])),
                'errors' => null,
                'code' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[OrderController@store] Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['receipt', 'invoice', 'attachments']),
                'file_uploads' => [
                    'has_receipt' => $request->hasFile('receipt'),
                    'has_invoice' => $request->hasFile('invoice'),
                    'has_attachments' => $request->hasFile('attachments')
                ]
            ]);
            
            return response()->json([
                'message' => 'Failed to create order: ' . $e->getMessage(),
                'data' => null,
                'errors' => [
                    'server' => [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'code' => $e->getCode()
                    ]
                ],
                'code' => 500,
            ], 500);
        }
    }

    public function update(OrderUpdateRequest $request, $id)
    {
        try {
            $order = Order::where('user_id', Auth::id())->findOrFail($id);

            // Pass the order to the request for validation
            $request->merge(['order' => $order]);

            DB::beginTransaction();

            $data = $request->validated();
            $order->update($data);

            // Update or create order items
            if ($request->has('items') && is_array($request->items)) {
                $this->updateOrderItems($order, $request->items);
            }

            // Handle file uploads
            if ($request->hasFile('receipt')) {
                $order->setReceipt($request->file('receipt'));
            }

            if ($request->hasFile('invoice')) {
                $order->setInvoice($request->file('invoice'));
            }

            if ($request->hasFile('attachments')) {
                $order->setAttachment($request->file('attachments'));
            }

            DB::commit();

            return response()->json([
                'message' => 'Order updated successfully.',
                'data' => new OrderResource($order->fresh(['user', 'items.product'])),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Order not found.',
                'data' => null,
                'errors' => ['order' => ['Order could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update order.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $order = Order::where('user_id', Auth::id())->with('items')->findOrFail($id);

            DB::beginTransaction();

            // Delete all associated order items
            if ($order->items->isNotEmpty()) {
                $order->items()->delete();
            }

            // Get order data before deletion for notification
            $orderData = $order->replicate();

            // Delete the order and its media
            $order->delete();
            $order->setReceipt(null);
            $order->setInvoice(null);
            $order->setAttachment([]);

            DB::commit();

            // Send notification to user that their order was deleted
            /**
             * @var \App\Models\User $user
             */
            $user = Auth::user();
            $user->notify(new OrderCancelledNotification(
                $orderData,
                'system',
                'Order was permanently deleted'
            ));

            // Dispatch broadcast event
            OrderCancelledEvent::dispatch($orderData, 'system', 'Order was permanently deleted');

            return response()->json([
                'message' => 'Order deleted successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Order not found.',
                'data' => null,
                'errors' => ['order' => ['Order could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete order.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    public function markAsCancelled($id)
    {
        try {
            $order = Order::where('user_id', Auth::id())->findOrFail($id);

            if (!$order->canBeCancelled()) {
                return response()->json([
                    'message' => 'Order cannot be cancelled.',
                    'data' => null,
                    'errors' => ['order' => ['Order status does not allow cancellation.']],
                    'code' => 400,
                ], 400);
            }

            DB::beginTransaction();

            $order->markAsCancelled();

            DB::commit();

            // Send notification to user that their order was cancelled
            /**
             * @var \App\Models\User $user
             */
            $user = Auth::user();
            $user->notify(new OrderCancelledNotification(
                $order,
                'customer',
                'You cancelled this order'
            ));

            // Dispatch broadcast event
            OrderCancelledEvent::dispatch($order, 'customer', 'You cancelled this order');

            return response()->json([
                'message' => 'Order cancelled successfully.',
                'data' => new OrderResource($order->fresh(['user', 'items.product'])),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Order not found.',
                'data' => null,
                'errors' => ['order' => ['Order could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to cancel order.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    public function markAsDelivered($id)
    {
        try {
            $order = Order::where('user_id', Auth::id())->findOrFail($id);

            if (!$order->canBeCancelled()) {
                return response()->json([
                    'message' => 'Order cannot be marked as delivered.',
                    'data' => null,
                    'errors' => ['order' => ['Order status does not allow this action.']],
                    'code' => 400,
                ], 400);
            }

            $order->markAsDelivered();

            return response()->json([
                'message' => 'Order marked as delivered successfully.',
                'data' => new OrderResource($order->fresh(['user', 'items.product'])),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Order not found.',
                'data' => null,
                'errors' => ['order' => ['Order could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to mark order as delivered.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Save order items for a new order.
     *
     * @param \App\Models\Order $order
     * @param array $items
     * @return void
     */
    protected function saveOrderItems(Order $order, array $items)
    {
        $orderItems = [];

        foreach ($items as $item) {
            $orderItems[] = [
                'product_detail_id' => $item['product_detail_id'],
                'product_name' => $item['product_name'],
                'product_sku' => $item['product_sku'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $order->items()->createMany($orderItems);
    }

    /**
     * Update order items for an existing order.
     *
     * @param \App\Models\Order $order
     * @param array $items
     * @return void
     */
    protected function updateOrderItems(Order $order, array $items)
    {
        // Recalculate order amounts with updated items
        $orderData = $this->calculateOrderAmounts($items);

        // Update order totals
        $order->update([
            'subtotal' => $orderData['subtotal'],
            'shipping_cost' => $orderData['shipping_cost'],
            'tax_amount' => $orderData['tax_amount'],
            'total_amount' => $orderData['total_amount'] - $order->discount_amount, // Apply existing discount
            'currency' => $orderData['currency'],
        ]);

        $existingItemIds = [];

        foreach ($orderData['items'] as $item) {
            $itemData = [
                'product_detail_id' => $item['product_detail_id'],
                'product_name' => $item['product_name'],
                'product_sku' => $item['product_sku'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price'],
                'updated_at' => now(),
            ];

            if (isset($item['id'])) {
                // Update existing item
                $order->items()->where('id', $item['id'])->update($itemData);
                $existingItemIds[] = $item['id'];
            } else {
                // Create new item
                $newItem = $order->items()->create($itemData);
                $existingItemIds[] = $newItem->id;
            }
        }

        // Delete items that were not included in the update
        if (!empty($existingItemIds)) {
            $order->items()->whereNotIn('id', $existingItemIds)->delete();
        }

        return $order->fresh(['items']);
    }

    /**
     * Generate a unique order number.
     *
     * @return string
     */
    /**
     * Get application settings with fallback to defaults
     *
     * @return array
     */
    protected function getOrderSettings()
    {
        return [
            'currency' => Setting::where('key', 'currency')->value('value') ?? 'EGP',
            'tax_rate' => (float)(Setting::where('key', 'tax_rate')->value('value') ?? 0.0), // 15% default
            'shipping_rate' => (float)(Setting::where('key', 'shipping_rate')->value('value') ?? 0.1), // 10% default
            'min_shipping_cost' => (float)(Setting::where('key', 'min_shipping_cost')->value('value') ?? null),
            'max_shipping_cost' => (float)(Setting::where('key', 'max_shipping_cost')->value('value') ?? null),
        ];
    }

    /**
     * Calculate order amounts based on items and settings
     *
     * @param array $items
     * @param array $settings
     * @return array
     */
    protected function calculateOrderAmounts($items, $settings = null, $includeTax = true)
    {
        $settings = $settings ?? $this->getOrderSettings();
        $subtotal = 0;
        $totalDiscount = 0;
        $calculatedItems = [];

        // First pass: Calculate subtotal with product discounts
        foreach ($items as $item) {
            $productDetail = ProductDetail::with('product')->findOrFail($item['product_detail_id']);
            $quantity = $item['quantity'];
            
            // Calculate unit price after applying product discount if any
            $unitPrice = $productDetail->price;
            $discount = 0;
            
            // Apply product discount if available (fixed amount)
            if ($productDetail->discount > 0) {
                // Ensure discount doesn't make price negative
                $discount = min($productDetail->discount, $unitPrice);
                $unitPriceAfterDiscount = $unitPrice - $discount;
            } else {
                $unitPriceAfterDiscount = $unitPrice;
                $discount = 0;
            }
            
            $itemTotal = $unitPriceAfterDiscount * $quantity;
            $itemDiscount = $discount * $quantity;
            
            $calculatedItems[] = [
                'product_detail_id' => $productDetail->id,
                'product_name' => $productDetail->product->name,
                'product_sku' => $productDetail->sku,
                'quantity' => $quantity,
                'unit_price' => $unitPriceAfterDiscount,
                'original_unit_price' => $unitPrice, // Store original price for reference
                'discount_amount' => $itemDiscount,
                'total_price' => $itemTotal,
            ];

            $subtotal += $itemTotal;
            $totalDiscount += $itemDiscount;
        }

        // Calculate shipping cost as a percentage of subtotal with min/max limits
        $shippingRate = is_numeric($settings['shipping_rate']) ? (float)$settings['shipping_rate'] : 0;
        $minShippingCost = is_numeric($settings['min_shipping_cost']) ? (float)$settings['min_shipping_cost'] : 0;
        $maxShippingCost = is_numeric($settings['max_shipping_cost']) ? (float)$settings['max_shipping_cost'] : PHP_FLOAT_MAX;
        
        // Calculate base shipping cost
        $shippingCost = $subtotal * $shippingRate;
        
        // Apply min/max shipping cost limits
        $shippingCost = max($minShippingCost, min($maxShippingCost, $shippingCost));

        // Calculate tax as a percentage of subtotal (after product discounts)
        $taxRate = is_numeric($settings['tax_rate']) ? (float)$settings['tax_rate'] : 0;
        $taxAmount = $includeTax ? $subtotal * $taxRate : 0;

        // Calculate total amount (tax included only if $includeTax is true)
        $totalAmount = $subtotal + $shippingCost + $taxAmount;

        return [
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'currency' => $settings['currency'],
            'items' => $calculatedItems,
        ];
    }

    /**
     * Generate a unique order number
     *
     * @return string
     */
    protected function generateOrderNumber()
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(8));
    }
}
