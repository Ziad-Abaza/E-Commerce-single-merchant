<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Http\Resources\PaymentResource;
use App\Http\Requests\PaymentStoreRequest;
use App\Http\Requests\PaymentUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the payments.
     */
    public function index(Request $request)
    {
        try {
            $query = Payment::with(['order.user']);

            // Filter by order if specified
            if ($request->has('order_id')) {
                $query->where('order_id', $request->order_id);
            }

            // Filter by status if specified
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by payment method if specified
            if ($request->has('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }

            // Filter by date range if specified
            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $payments = $query->orderBy('created_at', 'desc')->paginate(15);

            return response()->json([
                'message' => 'Payments retrieved successfully.',
                'data' => PaymentResource::collection($payments),
                'pagination' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'per_page' => $payments->perPage(),
                    'total' => $payments->total(),
                ],
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve payments.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Display the specified payment.
     */
    public function show($id)
    {
        try {
            $payment = Payment::with(['order.user'])->findOrFail($id);

            return response()->json([
                'message' => 'Payment retrieved successfully.',
                'data' => new PaymentResource($payment),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Payment not found.',
                'data' => null,
                'errors' => ['payment' => ['Payment could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve payment.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Store a newly created payment.
     */
    public function store(PaymentStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $payment = Payment::create($data);

            // Handle file uploads
            if ($request->hasFile('receipt')) {
                $payment->setReceipt($request->file('receipt'));
            }

            if ($request->hasFile('proof_of_payment')) {
                $payment->setProofOfPayment($request->file('proof_of_payment'));
            }

            if ($request->hasFile('document')) {
                $payment->setDocument($request->file('document'));
            }

            DB::commit();

            return response()->json([
                'message' => 'Payment created successfully.',
                'data' => new PaymentResource($payment->load(['order.user'])),
                'errors' => null,
                'code' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create payment.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Update the specified payment.
     */
    public function update(PaymentUpdateRequest $request, $id)
    {
        try {
            $payment = Payment::findOrFail($id);

            DB::beginTransaction();

            $data = $request->validated();
            $payment->update($data);

            // Handle file uploads
            if ($request->hasFile('receipt')) {
                $payment->setReceipt($request->file('receipt'));
            }

            if ($request->hasFile('proof_of_payment')) {
                $payment->setProofOfPayment($request->file('proof_of_payment'));
            }

            if ($request->hasFile('document')) {
                $payment->setDocument($request->file('document'));
            }

            DB::commit();

            return response()->json([
                'message' => 'Payment updated successfully.',
                'data' => new PaymentResource($payment->fresh(['order.user'])),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Payment not found.',
                'data' => null,
                'errors' => ['payment' => ['Payment could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update payment.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Remove the specified payment.
     */
    public function destroy($id)
    {
        try {
            $payment = Payment::findOrFail($id);

            DB::beginTransaction();

            $payment->delete();
            $payment->setReceipt(null);
            $payment->setProofOfPayment(null);
            $payment->setDocument(null);

            DB::commit();

            return response()->json([
                'message' => 'Payment deleted successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Payment not found.',
                'data' => null,
                'errors' => ['payment' => ['Payment could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete payment.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Mark payment as completed.
     */
    public function markAsCompleted($id)
    {
        try {
            $payment = Payment::findOrFail($id);

            if ($payment->isCompleted()) {
                return response()->json([
                    'message' => 'Payment is already completed.',
                    'data' => null,
                    'errors' => ['payment' => ['Payment status is already completed.']],
                    'code' => 400,
                ], 400);
            }

            $payment->markAsCompleted();

            return response()->json([
                'message' => 'Payment marked as completed successfully.',
                'data' => new PaymentResource($payment->fresh(['order.user'])),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Payment not found.',
                'data' => null,
                'errors' => ['payment' => ['Payment could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to mark payment as completed.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed($id)
    {
        try {
            $payment = Payment::findOrFail($id);

            if ($payment->isCompleted()) {
                return response()->json([
                    'message' => 'Completed payment cannot be marked as failed.',
                    'data' => null,
                    'errors' => ['payment' => ['Payment status is completed and cannot be changed.']],
                    'code' => 400,
                ], 400);
            }

            $payment->markAsFailed();

            return response()->json([
                'message' => 'Payment marked as failed successfully.',
                'data' => new PaymentResource($payment->fresh(['order.user'])),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Payment not found.',
                'data' => null,
                'errors' => ['payment' => ['Payment could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to mark payment as failed.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Mark payment as refunded.
     */
    public function markAsRefunded($id)
    {
        try {
            $payment = Payment::findOrFail($id);

            if (!$payment->isCompleted()) {
                return response()->json([
                    'message' => 'Only completed payments can be refunded.',
                    'data' => null,
                    'errors' => ['payment' => ['Payment must be completed before refunding.']],
                    'code' => 400,
                ], 400);
            }

            $payment->markAsRefunded();

            return response()->json([
                'message' => 'Payment marked as refunded successfully.',
                'data' => new PaymentResource($payment->fresh(['order.user'])),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Payment not found.',
                'data' => null,
                'errors' => ['payment' => ['Payment could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to mark payment as refunded.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }
}
