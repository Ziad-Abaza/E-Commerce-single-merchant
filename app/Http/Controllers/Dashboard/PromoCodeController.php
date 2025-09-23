<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class PromoCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'search' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        $query = PromoCode::with('product');

        // Filter by product if provided
        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Search by code
        if ($request->has('search') && !empty($request->search)) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->per_page ?? 15;
        $promoCodes = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $promoCodes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'code' => 'required|string|max:50|unique:promo_codes,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $promoCode = PromoCode::create([
                'product_id' => $request->product_id,
                'code' => strtoupper($request->code),
                'type' => $request->type,
                'value' => $request->value,
                'usage_limit' => $request->usage_limit,
                'usage_count' => 0,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => $request->is_active ?? true,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Promo code created successfully',
                'data' => $promoCode,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create promo code',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $promoCode = PromoCode::with('product')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $promoCode,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Promo code not found',
            ], 404);
        }
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'code' => 'sometimes|string|max:50|unique:promo_codes,code,' . $id,
            'type' => 'sometimes|in:percentage,fixed',
            'value' => 'sometimes|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $promoCode = PromoCode::findOrFail($id);
            
            $promoCode->update([
                'code' => $request->has('code') ? strtoupper($request->code) : $promoCode->code,
                'type' => $request->type ?? $promoCode->type,
                'value' => $request->value ?? $promoCode->value,
                'usage_limit' => $request->usage_limit ?? $promoCode->usage_limit,
                'start_date' => $request->start_date ?? $promoCode->start_date,
                'end_date' => $request->end_date ?? $promoCode->end_date,
                'is_active' => $request->has('is_active') ? $request->is_active : $promoCode->is_active,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Promo code updated successfully',
                'data' => $promoCode,
            ]);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Promo code not found',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update promo code',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $promoCode = PromoCode::findOrFail($id);
            $promoCode->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Promo code deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Promo code not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete promo code',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
