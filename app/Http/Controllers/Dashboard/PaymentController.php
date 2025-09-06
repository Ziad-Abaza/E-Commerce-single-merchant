<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use App\Http\Resources\PaymentResource;
use App\Http\Requests\PaymentUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'nullable|in:pending,completed,failed,cancelled,refunded',
            'payment_method' => 'nullable|string|max:255',
            'order_id' => 'nullable|exists:orders,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort' => 'nullable|in:created_at,updated_at,amount,status',
            'order' => 'nullable|in:asc,desc',
        ]);

        $query = Payment::with(['order.user']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->payment_method) {
            $query->where('payment_method', 'like', '%' . $request->payment_method . '%');
        }

        // Filter by order
        if ($request->order_id) {
            $query->where('order_id', $request->order_id);
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
        $payments = $query->paginate($perPage);

        return response()->json([
            'message' => 'Payments retrieved successfully.',
            'data' => PaymentResource::collection($payments->items()),
            'pagination' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
            ],
            'code' => 200,
        ]);
    }

    /**
     * Display the specified payment
     */
    public function show($id): JsonResponse
    {
        $payment = Payment::with(['order.user', 'order.orderItems.product'])
            ->findOrFail($id);

        return response()->json([
            'message' => 'Payment retrieved successfully.',
            'data' => new PaymentResource($payment),
            'code' => 200,
        ]);
    }

    /**
     * Update the specified payment
     */
    public function update(PaymentUpdateRequest $request, $id): JsonResponse
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validated();
        $payment->update($validated);

        return response()->json([
            'message' => 'Payment updated successfully.',
            'data' => new PaymentResource($payment->fresh(['order.user'])),
            'code' => 200,
        ]);
    }

    /**
     * Update payment status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,completed,failed,cancelled,refunded',
            'notes' => 'nullable|string|max:1000',
        ]);

        $payment = Payment::findOrFail($id);
        
        $oldStatus = $payment->status;
        $payment->update([
            'status' => $request->status,
            'notes' => $request->notes ?? $payment->notes,
        ]);

        // If payment is completed, update order status
        if ($request->status === 'completed' && $payment->order) {
            $payment->order->update(['status' => 'processing']);
        }

        // Log status change
        activity()
            ->performedOn($payment)
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'notes' => $request->notes,
            ])
            ->log('Payment status updated');

        return response()->json([
            'message' => 'Payment status updated successfully.',
            'data' => new PaymentResource($payment->fresh(['order.user'])),
            'code' => 200,
        ]);
    }

    /**
     * Process refund for a payment
     */
    public function refund(Request $request, $id): JsonResponse
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0.01',
            'reason' => 'nullable|string|max:1000',
        ]);

        $payment = Payment::findOrFail($id);

        if ($payment->status !== 'completed') {
            return response()->json([
                'message' => 'Only completed payments can be refunded.',
                'code' => 400,
            ], 400);
        }

        $refundAmount = $request->amount ?? $payment->amount;
        
        if ($refundAmount > $payment->amount) {
            return response()->json([
                'message' => 'Refund amount cannot exceed payment amount.',
                'code' => 400,
            ], 400);
        }

        $payment->update([
            'status' => 'refunded',
            'refund_amount' => $refundAmount,
            'refund_reason' => $request->reason,
            'refunded_at' => now(),
        ]);

        // Update order status if full refund
        if ($refundAmount >= $payment->amount && $payment->order) {
            $payment->order->update(['status' => 'refunded']);
        }

        return response()->json([
            'message' => 'Payment refunded successfully.',
            'data' => new PaymentResource($payment->fresh(['order.user'])),
            'code' => 200,
        ]);
    }

    /**
     * Get payment statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_payments' => Payment::count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'completed_payments' => Payment::where('status', 'completed')->count(),
            'failed_payments' => Payment::where('status', 'failed')->count(),
            'cancelled_payments' => Payment::where('status', 'cancelled')->count(),
            'refunded_payments' => Payment::where('status', 'refunded')->count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'total_refunds' => Payment::where('status', 'refunded')->sum('refund_amount'),
            'net_revenue' => Payment::where('status', 'completed')->sum('amount') - Payment::where('status', 'refunded')->sum('refund_amount'),
        ];

        return response()->json([
            'message' => 'Payment statistics retrieved successfully.',
            'data' => $stats,
            'code' => 200,
        ]);
    }

    /**
     * Get payments by status
     */
    public function byStatus($status): JsonResponse
    {
        $validStatuses = ['pending', 'completed', 'failed', 'cancelled', 'refunded'];
        
        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'message' => 'Invalid status provided.',
                'code' => 400,
            ], 400);
        }

        $payments = Payment::with(['order.user'])
            ->where('status', $status)
            ->latest()
            ->paginate(15);

        return response()->json([
            'message' => "Payments with status '{$status}' retrieved successfully.",
            'data' => PaymentResource::collection($payments->items()),
            'pagination' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
            ],
            'code' => 200,
        ]);
    }

    /**
     * Get revenue analytics
     */
    public function revenueAnalytics(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'nullable|in:7_days,30_days,90_days,1_year',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $period = $request->period ?? '30_days';
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Set date range based on period
        if (!$startDate || !$endDate) {
            switch ($period) {
                case '7_days':
                    $startDate = Carbon::now()->subDays(7);
                    $endDate = Carbon::now();
                    break;
                case '30_days':
                    $startDate = Carbon::now()->subDays(30);
                    $endDate = Carbon::now();
                    break;
                case '90_days':
                    $startDate = Carbon::now()->subDays(90);
                    $endDate = Carbon::now();
                    break;
                case '1_year':
                    $startDate = Carbon::now()->subYear();
                    $endDate = Carbon::now();
                    break;
            }
        }

        // Get revenue data grouped by date
        $revenueData = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as daily_revenue'),
                DB::raw('COUNT(*) as payment_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get payment method distribution
        $paymentMethodDistribution = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total_amount'))
            ->groupBy('payment_method')
            ->get();

        // Calculate total revenue for the period
        $totalRevenue = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');

        return response()->json([
            'message' => 'Revenue analytics retrieved successfully.',
            'data' => [
                'period' => $period,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'revenue_data' => $revenueData,
                'payment_method_distribution' => $paymentMethodDistribution,
                'total_revenue' => $totalRevenue,
            ],
            'code' => 200,
        ]);
    }

    /**
     * Get failed payments that need attention
     */
    public function failedPayments(): JsonResponse
    {
        $failedPayments = Payment::with(['order.user'])
            ->where('status', 'failed')
            ->latest()
            ->paginate(15);

        return response()->json([
            'message' => 'Failed payments retrieved successfully.',
            'data' => PaymentResource::collection($failedPayments->items()),
            'pagination' => [
                'current_page' => $failedPayments->currentPage(),
                'last_page' => $failedPayments->lastPage(),
                'per_page' => $failedPayments->perPage(),
                'total' => $failedPayments->total(),
            ],
            'code' => 200,
        ]);
    }
}
