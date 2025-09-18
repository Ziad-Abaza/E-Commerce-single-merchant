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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Events\NewOrderEvent;
use App\Notifications\NewOrderNotification;

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
            $order = Order::with(['user', 'items.product'])
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

            $validated = $request->validate([
                'status' => 'sometimes|string|in:pending,processing,shipped,delivered,cancelled',
                'shipping_address' => 'sometimes|string|max:500',
                'notes' => 'nullable|string|max:1000',
            ]);

            $data['order_number'] = $this->generateOrderNumber();
            $data['user_id'] = Auth::id();

            $order = Order::create($data);

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

            $owners = \App\Models\User::role('owner')->get();
            Log::info('[OrderController@store] Notifying owners', ['owners' => $owners->pluck('id')]);

            foreach ($owners as $owner) {
                $owner->notify(new NewOrderNotification($order));
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
            return response()->json([
                'message' => 'Failed to create order.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    public function update(OrderUpdateRequest $request, $id)
    {
        try {
            $order = Order::where('user_id', Auth::id())->findOrFail($id);

            DB::beginTransaction();

            $data = $request->validated();
            $order->update($data);

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
            $order = Order::where('user_id', Auth::id())->findOrFail($id);

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

            $order->markAsCancelled();

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
            return response()->json([
                'message' => 'Failed to cancel order.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    private function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
