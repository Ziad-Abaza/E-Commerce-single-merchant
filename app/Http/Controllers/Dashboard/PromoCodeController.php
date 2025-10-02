<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\PromoCodeRequest;
use App\Http\Resources\PromoCodeResource;
use App\Models\PromoCode;
use App\Services\PromoCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PromoCodeController extends Controller
{
    protected $promoCodeService;

    public function __construct(PromoCodeService $promoCodeService)
    {
        $this->promoCodeService = $promoCodeService;
    }

    /**
     * Display a listing of promo codes
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $status = $request->input('status');
        $discountType = $request->input('discount_type');
        $targetType = $request->input('target_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $query = PromoCode::withCount('usages')
            ->with(['products', 'categories']);

        // Search by code or name
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if (in_array($status, ['active', 'inactive'])) {
            $query->where('is_active', $status === 'active');
        }

        // Filter by discount type
        if (in_array($discountType, ['percentage', 'fixed'])) {
            $query->where('discount_type', $discountType);
        }

        // Filter by target type
        if (in_array($targetType, ['products', 'categories', 'shipping', 'order'])) {
            $query->where('target_type', $targetType);
        }

        // Filter by date range
        if ($startDate) {
            $query->whereDate('start_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('end_date', '<=', $endDate);
        }

        // Apply sorting
        $validSortColumns = ['code', 'name', 'discount_value', 'total_usage_count', 'start_date', 'end_date', 'created_at'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'created_at';
        $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $promoCodes = $query->paginate($perPage);

        $target_types = ['products','categories','shipping','order'];
        $availableFilters = [
            'target_types' => PromoCode::pluck('target_type')->unique()->values()->toArray(),
            'discount_types' => ['fixed','percentage'],
            'status_options' => ['active','inactive']
        ];

        return response()->json([
            'success' => true,
            'data' => PromoCodeResource::collection($promoCodes),
            'message' => 'Promo codes retrieved successfully',
            'code' => 200,
            'pagination' => [
                'current_page' => $promoCodes->currentPage(),
                'per_page' => $promoCodes->perPage(),
                'total' => $promoCodes->total(),
                'last_page' => $promoCodes->lastPage(),
                'from' => $promoCodes->firstItem(),
                'to' => $promoCodes->lastItem(),
            ],
            'filters' => [
                'search' => $search,
                'status' => $status,
                'discount_type' => $discountType,
                'target_type' => $targetType,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
            'target_types' => $target_types,
            'available_filters' => $availableFilters,
        ], 200);
    }

    /**
     * Store a newly created promo code
     *
     * @param  PromoCodeRequest  $request
     * @return JsonResponse
     */
    public function store(PromoCodeRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $promoCode = $this->promoCodeService->createPromoCode($data);

            return response()->json([
                'success' => true,
                'data' => new PromoCodeResource($promoCode->load(['products', 'categories'])),
                'message' => 'Promo code created successfully',
                'code' => 201,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error in PromoCodeController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create promo code',
                'error' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Display the specified promo code
     *
     * @param  PromoCode  $promoCode
     * @return JsonResponse
     */
    public function show(PromoCode $promoCode): JsonResponse
    {
        try {
            $promoCode->load(['products', 'categories', 'usages']);

            return response()->json([
                'success' => true,
                'data' => new PromoCodeResource($promoCode),
                'message' => 'Promo code retrieved successfully',
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in PromoCodeController@show: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve promo code',
                'error' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Update the specified promo code
     *
     * @param  PromoCodeRequest  $request
     * @param  PromoCode  $promoCode
     * @return JsonResponse
     */
    public function update(PromoCodeRequest $request, PromoCode $promoCode): JsonResponse
    {
        try {
            $data = $request->validated();
            $promoCode = $this->promoCodeService->updatePromoCode($promoCode, $data);

            return response()->json([
                'success' => true,
                'data' => new PromoCodeResource($promoCode->load(['products', 'categories'])),
                'message' => 'Promo code updated successfully',
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in PromoCodeController@update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update promo code',
                'error' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Toggle the active status of the promo code
     *
     * @param  PromoCode  $promoCode
     * @return JsonResponse
     */
    public function toggleStatus(PromoCode $promoCode): JsonResponse
    {
        try {
            $promoCode->update(['is_active' => !$promoCode->is_active]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $promoCode->id,
                    'is_active' => $promoCode->fresh()->is_active
                ],
                'message' => 'Promo code status updated successfully',
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in PromoCodeController@toggleStatus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update promo code status',
                'error' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Remove the specified promo code
     *
     * @param  PromoCode  $promoCode
     * @return JsonResponse
     */
    public function destroy(PromoCode $promoCode): JsonResponse
    {
        try {
            $promoCode->delete();

            return response()->json([
                'success' => true,
                'message' => 'Promo code deleted successfully',
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in PromoCodeController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete promo code',
                'error' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Display a listing of trashed promo codes
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function trash(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        $query = PromoCode::onlyTrashed()
            ->withCount('usages')
            ->with(['products', 'categories']);

        // Search by code or name
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $promoCodes = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => PromoCodeResource::collection($promoCodes),
            'message' => 'Trashed promo codes retrieved successfully',
            'code' => 200,
            'pagination' => [
                'current_page' => $promoCodes->currentPage(),
                'per_page' => $promoCodes->perPage(),
                'total' => $promoCodes->total(),
                'last_page' => $promoCodes->lastPage(),
                'from' => $promoCodes->firstItem(),
                'to' => $promoCodes->lastItem(),
            ],
        ], 200);
    }

    /**
     * Restore the specified trashed promo code
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id): JsonResponse
    {
        try {
            $promoCode = PromoCode::onlyTrashed()->findOrFail($id);
            $promoCode->restore();

            return response()->json([
                'success' => true,
                'data' => new PromoCodeResource($promoCode->load(['products', 'categories'])),
                'message' => 'Promo code restored successfully',
                'code' => 200,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code not found in trash',
                'error' => 'The specified promo code could not be found in trash',
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error in PromoCodeController@restore: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore promo code',
                'error' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Permanently delete the specified promo code
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelete($id): JsonResponse
    {
        try {
            $promoCode = PromoCode::onlyTrashed()->findOrFail($id);
            $promoCode->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Promo code permanently deleted',
                'code' => 200,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code not found in trash',
                'error' => 'The specified promo code could not be found in trash',
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error in PromoCodeController@forceDelete: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete promo code',
                'error' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Get related data (products and categories) for promo code form
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRelatedData(): JsonResponse
    {
        try {
            $products = \App\Models\Product::select('id', 'name')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            $categories = \App\Models\Category::select('id', 'name')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $products,
                    'categories' => $categories,
                ],
                'message' => 'Related data retrieved successfully',
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in PromoCodeController@getRelatedData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve related data',
                'error' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }
}
