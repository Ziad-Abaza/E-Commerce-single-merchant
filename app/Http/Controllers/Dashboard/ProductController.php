<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json([
            'message' => 'Product list retrieved successfully.',
            'data' => ProductResource::collection($products),
            'errors' => null,
            'code' => 200,
        ], 200);
    }

    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);
            return response()->json([
                'message' => 'Product retrieved successfully.',
                'data' => new ProductResource($product),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found.',
                'data' => null,
                'errors' => ['product' => ['Product could not be found.']],
                'code' => 404,
            ], 404);
        }
    }

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
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create product.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

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
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found.',
                'data' => null,
                'errors' => ['product' => ['Product could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update product.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return response()->json([
                'message' => 'Product deleted successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found.',
                'data' => null,
                'errors' => ['product' => ['Product could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete product.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }
}
