<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Http\Resources\OrderResource;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(Request $request)
    {
        try {
            $query = Order::with(['user', 'items.product', 'payment']);

            // Filter by user if specified
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Filter by status if specified
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by payment status if specified
            if ($request->has('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            // Filter by date range if specified
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

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        try {
            $order = Order::with(['user', 'items.product', 'payment'])->findOrFail($id);

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

    /**
     * Store a newly created order.
     */
    public function store(OrderStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['order_number'] = $this->generateOrderNumber();

            $order = Order::create($data);

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
                'message' => 'Order created successfully.',
                'data' => new OrderResource($order->load(['user', 'items.product', 'payment'])),
                'errors' => null,
                'code' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create order.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Update the specified order.
     */
    public function update(OrderUpdateRequest $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            DB::beginTransaction();

            $data = $request->validated();
            $order->update($data);

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
                'data' => new OrderResource($order->fresh(['user', 'items.product', 'payment'])),
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

    /**
     * Remove the specified order.
     */
    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);

            DB::beginTransaction();

            $order->delete();
            $order->setReceipt(null);
            $order->setInvoice(null);
            $order->setAttachment([]);

            DB::commit();

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

    /**
     * Mark order as delivered.
     */
    public function markAsDelivered($id)
    {
        try {
            $order = Order::findOrFail($id);

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
                'data' => new OrderResource($order->fresh(['user', 'items.product', 'payment'])),
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
     * Mark order as cancelled.
     */
    public function markAsCancelled($id)
    {
        try {
            $order = Order::findOrFail($id);

            if (!$order->canBeCancelled()) {
                return response()->json([
                    'message' => 'Order cannot be cancelled.',
                    'data' => null,
                    'errors' => ['order' => ['Order status does not allow cancellation.']],
                    'code' => 400,
                ], 400);
            }

            $order->markAsCancelled();

            return response()->json([
                'message' => 'Order cancelled successfully.',
                'data' => new OrderResource($order->fresh(['user', 'items.product', 'payment'])),
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
                'message' => 'Failed to cancel order.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Generate a unique order number.
     */
    private function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
