<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductDetail;
use App\Http\Resources\OrderResource;
use App\Notifications\customer\OrderStatusUpdatedNotification;
use App\Events\OrderStatusUpdatedEvent;
use App\Http\Requests\OrderUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with filters and search
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'nullable|in:pending,processing,shipped,delivered,cancelled,refunded',
            'user_id' => 'nullable|exists:users,id',
            'search' => 'nullable|string|max:255', // search term
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort' => 'nullable|in:created_at,updated_at,total_amount,status',
            'order' => 'nullable|in:asc,desc',
        ]);

        $query = Order::with(['user', 'items.product']);

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

        // Search across order_number, notes, product names
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                    ->orWhere('notes', 'LIKE', "%{$search}%")
                    ->orWhereHas('items.product', function ($sub) use ($search) {
                        $sub->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Apply sorting
        $sortField = $request->sort ?? 'created_at';
        $sortOrder = $request->order ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        $perPage = $request->per_page ?? 15;
        $orders = $query->paginate($perPage);

        // ====== Statistics ======
        $stats = [
            'total_orders'   => Order::count(),
            'pending'        => Order::where('status', 'pending')->count(),
            'processing'     => Order::where('status', 'processing')->count(),
            'shipped'        => Order::where('status', 'shipped')->count(),
            'delivered'      => Order::where('status', 'delivered')->count(),
            'cancelled'      => Order::where('status', 'cancelled')->count(),
            'total_revenue'  => Order::whereIn('status', ['delivered', 'shipped'])->sum('total_amount'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully.',
            'data' => OrderResource::collection($orders->items()),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
            'statistics' => $stats,
            'code' => 200,
        ]);
    }

    /**
     * Display the specified order
     */
    public function show($id): JsonResponse
    {
        $order = Order::with(['user', 'items.product'])->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
                'code' => 404,
            ], 404);
        }

        // Validate order items (check if product exists or in stock)
        foreach ($order->items as $item) {
            if (!$item->productDetail || $item->productDetail->trashed()) {
                $item->status = 'unavailable';
            } elseif ($item->productDetail->isOutOfStock()) {
                $item->status = 'out_of_stock';
            }
        }

        return response()->json([
            'success' => true,
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
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
                'code' => 404,
            ], 404);
        }

        $validated = $request->validated();
        $order->update($validated);

        // Remove payment_status and payment_method logic
        unset($validated['payment_status'], $validated['payment_method']);

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully.',
            'data' => new OrderResource($order->fresh(['user', 'items.product'])),
            'code' => 200,
        ]);
    }

    /**
     * Summary of updateStatus
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return JsonResponse
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'notes' => 'nullable|string|max:1000',
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
                'code' => 404,
            ], 404);
        }

        // Check if products are still available before moving to "processing"
        if ($request->status === 'processing') {
            foreach ($order->items as $item) {
                if (!$item->productDetail || $item->productDetail->trashed()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Product {$item->product_name} is no longer available.",
                        'code' => 422,
                    ], 422);
                }
                if ($item->quantity > $item->productDetail->stock) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for {$item->product_name}.",
                        'code' => 422,
                    ], 422);
                }
            }
        }

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

        // Send notification
        $order->user->notify(new OrderStatusUpdatedNotification($order, $oldStatus, $request->notes));
        OrderStatusUpdatedEvent::dispatch($order, $oldStatus, $request->notes);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully.',
            'data' => new OrderResource($order->fresh(['user', 'items.product'])),
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

        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
                'code' => 404,
            ], 404);
        }

        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled in its current status.',
                'code' => 400,
            ], 400);
        }

        $oldStatus = $order->status;
        $order->update([
            'status' => 'cancelled',
            'notes' => $request->reason ?? $order->notes,
        ]);

        activity()
            ->performedOn($order)
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => 'cancelled',
                'reason' => $request->reason,
            ])
            ->log('Order cancelled');

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully.',
            'data' => new OrderResource($order->fresh(['user', 'items.product'])),
            'code' => 200,
        ]);
    }

}
