<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ProductDetail;
use App\Http\Resources\ProductDetailsResource;
use App\Http\Requests\ProductDetailStoreRequest;
use App\Http\Requests\ProductDetailUpdateRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class ProductDetailController extends Controller
{
    /**
     * Summary of index
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, Product $product)
    {
        // not found
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
                'data' => null,
                'errors' => ['product' => ['Product does not exist.']],
                'code' => 404,
            ], 404);
        }
        $query = $product->details()->with(['product', 'attributeValues.attribute']);
        $attributesByCategory = $this->getAttributesByCategory($product);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('color', 'like', "%$search%")
                    ->orWhere('sku_variant', 'like', "%$search%")
                    ->orWhere('variant_identifier', 'like', "%$search%");
            });
        }

        if ($request->filled('is_active')) {
            $isActive = $request->input('is_active');
            if ($isActive === 'active') {
                $query->where('is_active', true);
            } elseif ($isActive === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('in_stock')) {
            $inStock = $request->input('in_stock');
            if ($inStock === 'in_stock') {
                $query->where('stock', '>', 0);
            } elseif ($inStock === 'out_of_stock') {
                $query->where('stock', '=', 0);
            }
        }

        $details = $query->paginate($request->input('per_page', 10));

        return response()->json([
            'success' => true,
            'message' => 'Product variants list retrieved successfully.',
            'data' => ProductDetailsResource::collection($details),
            'attributes_by_category' => $attributesByCategory,
            'pagination' => [
                'total' => $details->total(),
                'count' => $details->count(),
                'per_page' => $details->perPage(),
                'current_page' => $details->currentPage(),
                'total_pages' => $details->lastPage(),
                'from' => $details->firstItem(),
                'to' => $details->lastItem(),
            ],
            'errors' => null,
            'code' => 200,
        ], 200);
    }

    /**
     * Summary of show
     * @param \App\Models\Product $product
     * @param \App\Models\ProductDetail $detail
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Product $product, ProductDetail $detail)
    {
        if ($detail->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Product detail does not belong to this product.',
                'data' => null,
                'errors' => ['product_detail' => ['Invalid product detail for this product.']],
                'code' => 404,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product variant retrieved successfully.',
            'data' => new ProductDetailsResource($detail->load(['product', 'attributeValues.attribute'])),
            'errors' => null,
            'code' => 200,
        ], 200);
    }

    /**
     * Summary of store
     * @param \App\Http\Requests\ProductDetailStoreRequest $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductDetailStoreRequest $request, Product $product)
    {
        try {
            $data = $request->validated();
            $data['product_id'] = $product->id;

            // Generate SKU variant if not provided
            if (empty($data['sku_variant'])) {
                $data['sku_variant'] = ProductDetail::generateSkuVariant(
                    $product->sku,
                    $data['size'] ?? null,
                    $data['color'] ?? null
                );
            }

            DB::beginTransaction();
            $detail = ProductDetail::create($data);

            if ($request->hasFile('images')) {
                $detail->setImages($request->file('images'));
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product variant created successfully.',
                'data' => new ProductDetailsResource($detail->load(['product', 'attributeValues.attribute'])),
                'errors' => null,
                'code' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product detail.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Summary of update
     * @param \App\Http\Requests\ProductDetailUpdateRequest $request
     * @param \App\Models\Product $product
     * @param \App\Models\ProductDetail $detail
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProductDetailUpdateRequest $request, Product $product, ProductDetail $detail)
    {
        if ($detail->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Product detail does not belong to this product.',
                'data' => null,
                'errors' => ['product_detail' => ['Invalid product detail for this product.']],
                'code' => 404,
            ], 404);
        }

        try {
            $data = $request->validated();

            // Generate SKU variant if not provided and either size or color has changed
            if (empty($data['sku_variant']) && 
                (isset($data['size']) && $data['size'] !== $detail->size) || 
                (isset($data['color']) && $data['color'] !== $detail->color)) {
                
                $data['sku_variant'] = ProductDetail::generateSkuVariant(
                    $product->sku,
                    $data['size'] ?? $detail->size,
                    $data['color'] ?? $detail->color
                );
            }

            DB::beginTransaction();
            $detail->update($data);

            if ($request->hasFile('images')) {
                $detail->setImages($request->file('images'));
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Product detail updated successfully',
                'data' => $detail->toArray(),
                'code' => 200
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product detail.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500
            ], 500);
        }
    }

    /**
     * Summary of destroy
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @param \App\Models\ProductDetail $detail
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Product $product, ProductDetail $detail)
    {
        if ($detail->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Product detail does not belong to this product.',
                'data' => null,
                'errors' => ['product_detail' => ['Invalid product detail for this product.']],
                'code' => 404,
            ], 404);
        }

        if ($detail->orderItems()->exists() && !$request->boolean('confirm')) {
            return response()->json([
                'success' => false,
                'message' => 'This product detail is linked to existing orders. Do you want to proceed with deletion?',
                'data' => null,
                'requires_confirmation' => true,
                'code' => 409,
            ], 409);
        }

        try {
            DB::beginTransaction();
            $detail->delete(); // soft delete
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product detail soft deleted successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to soft delete product detail.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Summary of trashed
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function trashed(Product $product){
        $perPage = request()->input('per_page', 10);
        $trashedDetails = $product->details()->onlyTrashed()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Trashed product details retrieved successfully.',
            'data' => $trashedDetails->items(),
            'pagination' => [
                'current_page' => $trashedDetails->currentPage(),
                'per_page' => $trashedDetails->perPage(),
                'total' => $trashedDetails->total(),
                'last_page' => $trashedDetails->lastPage(),
                'from' => $trashedDetails->firstItem(),
                'to' => $trashedDetails->lastItem(),
            ],
            'errors' => null,
            'code' => 200,
        ], 200);
    }

    /**
     * Summary of restore
     * @param \App\Models\Product $product
     * @param mixed $detailId
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(Product $product, $detailId)
    {
        $detail = ProductDetail::onlyTrashed()->where('id', $detailId)->first();
        if (!$detail || $detail->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Product detail does not belong to this product or is not deleted.',
                'data' => null,
                'errors' => ['product_detail' => ['Invalid or non-deleted product detail for this product.']],
                'code' => 404,
            ], 404);
        }

        try {
            $detail->restore();
            return response()->json([
                'success' => true,
                'message' => 'Product variant restored successfully.',
                'data' => new ProductDetailsResource($detail->load(['product', 'attributeValues.attribute'])),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore product detail.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Summary of forceDestroy
     * @param \App\Models\Product $product
     * @param mixed $detailId
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDestroy(Product $product, $detailId)
    {
        $detail = ProductDetail::withTrashed()->findOrFail($detailId);

        if ($detail->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Product detail does not belong to this product.',
                'data' => null,
                'errors' => ['product_detail' => ['Invalid product detail for this product.']],
                'code' => 404,
            ], 404);
        }

        if ($detail->orderItems()->exists() || $detail->cartItems()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete this product detail because it is linked to orders or carts.',
                'data' => null,
                'errors' => ['product_detail' => ['Detail is linked to other records.']],
                'code' => 409,
            ], 409);
        }

        try {
            DB::beginTransaction();
            $detail->setImages([]);
            $detail->forceDelete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product detail permanently deleted successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete product detail.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Fetch attributes grouped by category for a product
     *
     * @param Product $product
     * @return \Illuminate\Support\Collection
     */
    protected function getAttributesByCategory(Product $product)
    {
        return $product->categories()
            ->with(['attributes' => function ($query) {
                $query->with(['values' => function ($query) {
                    $query->orderBy('value');
                }])
                    ->orderBy('attribute_category.sort_order');
            }])
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'attributes' => $category->attributes->map(function ($attribute) {
                        return [
                            'id' => $attribute->id,
                            'name' => $attribute->name,
                            'slug' => $attribute->slug,
                            'type' => $attribute->type,
                            'is_required' => (bool) $attribute->pivot->is_required,
                            'is_filterable' => $attribute->is_filterable,
                            'is_visible_on_frontend' => $attribute->is_visible_on_frontend,
                            'sort_order' => $attribute->pivot->sort_order,
                            'options' => $attribute->options,
                            'values' => $attribute->values->map(function ($value) {
                                return [
                                    'id' => $value->id,
                                    'value' => $value->value,
                                    'slug' => $value->slug,
                                ];
                            }),
                        ];
                    })->sortBy('sort_order')->values(),
                ];
            });
    }
}
