<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Resources\OrderResource;
use App\Http\Requests\OrderUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'nullable|in:pending,processing,shipped,delivered,cancelled,refunded',
            'user_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort' => 'nullable|in:created_at,updated_at,total_amount,status',
            'order' => 'nullable|in:asc,desc',
        ]);

        $query = Order::with(['user', 'orderItems.product', 'payment']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by user
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Apply sorting
        $sortField = $request->sort ?? 'created_at';
        $sortOrder = $request->order ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        $perPage = $request->per_page ?? 15;
        $orders = $query->paginate($perPage);

        return response()->json([
            'message' => 'Orders retrieved successfully.',
            'data' => OrderResource::collection($orders->items()),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
            'code' => 200,
        ]);
    }

    /**
     * Display the specified order
     */
    public function show($id): JsonResponse
    {
        $order = Order::with(['user', 'orderItems.product', 'payment'])
            ->findOrFail($id);

        return response()->json([
            'message' => 'Order retrieved successfully.',
            'data' => new OrderResource($order),
            'code' => 200,
        ]);
    }

    /**
     * Update the specified order
     */
    public function update(OrderUpdateRequest $request, $id): JsonResponse
    {
        $order = Order::findOrFail($id);

        $validated = $request->validated();
        $order->update($validated);

        return response()->json([
            'message' => 'Order updated successfully.',
            'data' => new OrderResource($order->fresh(['user', 'orderItems.product', 'payment'])),
            'code' => 200,
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'notes' => 'nullable|string|max:1000',
        ]);

        $order = Order::findOrFail($id);
        
        $oldStatus = $order->status;
        $order->update([
            'status' => $request->status,
            'notes' => $request->notes ?? $order->notes,
        ]);

        // Log status change
        activity()
            ->performedOn($order)
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'notes' => $request->notes,
            ])
            ->log('Order status updated');

        return response()->json([
            'message' => 'Order status updated successfully.',
            'data' => new OrderResource($order->fresh(['user', 'orderItems.product', 'payment'])),
            'code' => 200,
        ]);
    }

    /**
     * Cancel an order
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        $order = Order::findOrFail($id);

        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'message' => 'Order cannot be cancelled in its current status.',
                'code' => 400,
            ], 400);
        }

        $order->update([
            'status' => 'cancelled',
            'notes' => $request->reason ?? $order->notes,
        ]);

        // Log cancellation
        activity()
            ->performedOn($order)
            ->withProperties([
                'old_status' => $order->status,
                'new_status' => 'cancelled',
                'reason' => $request->reason,
            ])
            ->log('Order cancelled');

        return response()->json([
            'message' => 'Order cancelled successfully.',
            'data' => new OrderResource($order->fresh(['user', 'orderItems.product', 'payment'])),
            'code' => 200,
        ]);
    }

    /**
     * Get order statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'shipped_orders' => Order::where('status', 'shipped')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'refunded_orders' => Order::where('status', 'refunded')->count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total_amount'),
            'average_order_value' => Order::where('status', 'delivered')->avg('total_amount'),
        ];

        return response()->json([
            'message' => 'Order statistics retrieved successfully.',
            'data' => $stats,
            'code' => 200,
        ]);
    }

    /**
     * Get orders by status
     */
    public function byStatus($status): JsonResponse
    {
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];
        
        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'message' => 'Invalid status provided.',
                'code' => 400,
            ], 400);
        }

        $orders = Order::with(['user', 'orderItems.product', 'payment'])
            ->where('status', $status)
            ->latest()
            ->paginate(15);

        return response()->json([
            'message' => "Orders with status '{$status}' retrieved successfully.",
            'data' => OrderResource::collection($orders->items()),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
            'code' => 200,
        ]);
    }

    /**
     * Get order items for a specific order
     */
    public function orderItems($id): JsonResponse
    {
        $order = Order::findOrFail($id);
        $orderItems = $order->orderItems()->with('product')->get();

        return response()->json([
            'message' => 'Order items retrieved successfully.',
            'data' => $orderItems,
            'code' => 200,
        ]);
    }

    /**
     * Get order history/activity
     */
    public function history($id): JsonResponse
    {
        $order = Order::findOrFail($id);
        
        // This would require implementing activity logging
        // For now, return basic order information
        $history = [
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'status_changes' => [
                [
                    'status' => $order->status,
                    'updated_at' => $order->updated_at,
                ]
            ]
        ];

        return response()->json([
            'message' => 'Order history retrieved successfully.',
            'data' => $history,
            'code' => 200,
        ]);
    }
}
