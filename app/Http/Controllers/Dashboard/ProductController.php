<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Summary of index
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);

        $categories = Category::all('id', 'name');
        $products = Product::with('categories');

        if ($request->has('search')) {
            $query = $request->input('search');
            $products->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            });
        }

        if ($request->filled('category_id')) {
            $category_id = $request->input('category_id');
            $products->whereHas('categories', function ($q) use ($category_id) {
                $q->where('categories.id', $category_id);
            });
        }

        if ($request->filled('status')) {
            $state = $request->input('status');
            if ($state === 'active') {
                $products->where('is_active', true);
            } elseif ($state === 'inactive') {
                $products->where('is_active', false);
            } elseif ($state === 'low_stock') {
                $products->where('stock', '<', 5);
            }
        }

        $products = $products->paginate($perPage);

        return response()->json([
            'message' => 'Product list retrieved successfully.',
            'data' => ProductResource::collection($products),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
            'categories' => $categories,
            'errors' => null,
            'success' => true,
            'code' => 200,
        ], 200);
    }

    /**
     * Summary of show
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $product = Product::with('categories', 'details', 'reviews')->findOrFail($id);
            return response()->json([
                'message' => 'Product retrieved successfully.',
                'data' => new ProductResource($product),
                'errors' => null,
                'code' => 200,
                'success' => true,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found.',
                'data' => null,
                'errors' => ['product' => ['Product could not be found.']],
                'code' => 404,
                'success' => false,
            ], 404);
        }
    }

    /**
     * Summary of store
     * @param \App\Http\Requests\ProductStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductStoreRequest $request)
    {
        try {
            $data = $request->validated();
            DB::beginTransaction();
            $product = Product::create($data);
            DB::commit();
            return response()->json([
                'message' => 'Product created successfully.',
                'data' => new ProductResource($product),
                'errors' => null,
                'code' => 201,
                'success' => true,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create product.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
                'success' => false,
            ], 500);
        }
    }

    /**
     * Summary of update
     * @param \App\Http\Requests\ProductUpdateRequest $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProductUpdateRequest $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $data = $request->validated();
            DB::beginTransaction();
            $product->update($data);
            DB::commit();
            return response()->json([
                'message' => 'Product updated successfully.',
                'data' => new ProductResource($product),
                'errors' => null,
                'code' => 200,
                'success' => true,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found.',
                'data' => null,
                'errors' => ['product' => ['Product could not be found.']],
                'code' => 404,
                'success' => false,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update product.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
                'success' => false,
            ], 500);
        }
    }

    /**
     * Summary of destroy
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $hasOrders = $product->orderItems()->exists();

            if ($hasOrders && !$request->boolean('confirm')) {
                return response()->json([
                    'message' => 'This product is linked to existing orders. Confirmation required to delete.',
                    'data' => null,
                    'errors' => ['product' => ['Product is linked to orders.']],
                    'requires_confirmation' => true,
                    'code' => 409,
                    'success' => false,
                ], 409);
            }

            DB::beginTransaction();
            $product->delete(); // soft delete
            DB::commit();

            return response()->json([
                'message' => 'Product deleted successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
                'success' => true,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found.',
                'data' => null,
                'errors' => ['product' => ['Product could not be found.']],
                'code' => 404,
                'success' => false,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete product.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
                'success' => false,
            ], 500);
        }
    }

    /**
     * Summary of trash
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function trash(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $products = Product::onlyTrashed()->paginate($perPage);

        return response()->json([
            'message' => 'Trashed products retrieved successfully.',
            'data' => ProductResource::collection($products),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
            'errors' => null,
            'success' => true,
            'code' => 200,
        ], 200);
    }

    /**
     * Summary of restore
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        try {
            $product = Product::onlyTrashed()->findOrFail($id);
            $product->restore();

            return response()->json([
                'message' => 'Product restored successfully.',
                'data' => new ProductResource($product),
                'errors' => null,
                'code' => 200,
                'success' => true,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found in trash.',
                'data' => null,
                'errors' => ['product' => ['Product could not be found in trash.']],
                'code' => 404,
                'success' => false,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to restore product.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
                'success' => false,
            ], 500);
        }
    }

    /**
     * Summary of forceDelete
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelete($id)
    {
        try {
            $product = Product::withTrashed()->findOrFail($id);

            if ($product->deleted_at === null) {
                return response()->json([
                    'message' => 'Product not found in trash.',
                    'data' => null,
                    'errors' => ['product' => ['Product could not be found in trash.']],
                    'code' => 404,
                    'success' => false,
                ], 404);
            }

            if ($product->orderItems()->exists()) {
                return response()->json([
                    'message' => 'Cannot permanently delete product linked to existing orders.',
                    'data' => null,
                    'errors' => ['product' => ['Product is linked to orders.']],
                    'code' => 409,
                    'success' => false,
                ], 409);
            }

            $product->forceDelete();

            return response()->json([
                'message' => 'Product permanently deleted.',
                'data' => null,
                'errors' => null,
                'code' => 200,
                'success' => true,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found.',
                'data' => null,
                'errors' => ['product' => ['Product could not be found.']],
                'code' => 404,
                'success' => false,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to permanently delete product.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
                'success' => false,
            ], 500);
        }
    }
}
