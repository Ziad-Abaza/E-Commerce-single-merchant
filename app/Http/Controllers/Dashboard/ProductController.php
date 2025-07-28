<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\ProductResource;
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

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:products,slug',
                'brand' => 'nullable|string|max:255',
                'short_description' => 'nullable|string',
                'description' => 'nullable|string',
                'sku' => 'required|string|max:100|unique:products,sku',
                'is_active' => 'boolean',
            ]);
            DB::beginTransaction();
            $product = Product::create($data);
            DB::commit();
            return response()->json([
                'message' => 'Product created successfully.',
                'data' => new ProductResource($product),
                'errors' => null,
                'code' => 201,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'data' => null,
                'errors' => $e->errors(),
                'code' => 422,
            ], 422);
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

    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $data = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'slug' => 'sometimes|required|string|max:255|unique:products,slug,' . $id,
                'brand' => 'nullable|string|max:255',
                'short_description' => 'nullable|string',
                'description' => 'nullable|string',
                'sku' => 'sometimes|required|string|max:100|unique:products,sku,' . $id,
                'is_active' => 'boolean',
            ]);
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
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'data' => null,
                'errors' => $e->errors(),
                'code' => 422,
            ], 422);
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
