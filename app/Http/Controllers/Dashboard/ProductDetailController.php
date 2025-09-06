<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ProductDetail;
use App\Http\Resources\ProductDetailResource;
use App\Http\Requests\ProductDetailStoreRequest;
use App\Http\Requests\ProductDetailUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductDetailController extends Controller
{
    /**
     * Display a listing of the product details.
     */
    public function index()
    {
        $details = ProductDetail::with('product')->get();

        return response()->json([
            'message' => 'Product details list retrieved successfully.',
            'data' => ProductDetailResource::collection($details),
            'errors' => null,
            'code' => 200,
        ], 200);
    }

    /**
     * Display the specified product detail.
     */
    public function show($id)
    {
        try {
            $detail = ProductDetail::with('product')->findOrFail($id);

            return response()->json([
                'message' => 'Product detail retrieved successfully.',
                'data' => new ProductDetailResource($detail),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product detail not found.',
                'data' => null,
                'errors' => ['product_detail' => ['Product detail could not be found.']],
                'code' => 404,
            ], 404);
        }
    }

    /**
     * Store a newly created product detail with images.
     */
    public function store(ProductDetailStoreRequest $request)
    {
        try {
            $data = $request->validated();

            DB::beginTransaction();

            $detail = ProductDetail::create($data);

            if ($request->hasFile('images')) {
                $detail->setImages($request->file('images'));
            }

            DB::commit();

            return response()->json([
                'message' => 'Product detail created successfully.',
                'data' => new ProductDetailResource($detail->load('product')),
                'errors' => null,
                'code' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create product detail.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Update the specified product detail with images.
     */
    public function update(ProductDetailUpdateRequest $request, $id)
    {
        try {
            $detail = ProductDetail::findOrFail($id);

            $data = $request->validated();

            DB::beginTransaction();

            $detail->update($data);

            if ($request->hasFile('images')) {
                $detail->setImages($request->file('images'));
            }

            DB::commit();

            return response()->json([
                'message' => 'Product detail updated successfully.',
                'data' => new ProductDetailResource($detail->fresh('product')),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product detail not found.',
                'data' => null,
                'errors' => ['product_detail' => ['Product detail could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update product detail.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Remove the specified product detail.
     */
    public function destroy($id)
    {
        try {
            $detail = ProductDetail::findOrFail($id);

            DB::beginTransaction();

            $detail->delete();
            $detail->setImages([]);
            DB::commit();

            return response()->json([
                'message' => 'Product detail deleted successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product detail not found.',
                'data' => null,
                'errors' => ['product_detail' => ['Product detail could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete product detail.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Remove a specific image from the product detail.
     */
    public function removeImage(Request $request, $id)
    {
        try {
            $detail = ProductDetail::findOrFail($id);
            $mediaId = $request->input('media_id');

            $media = $detail->getMedia('images')->where('id', $mediaId)->first();

            if (!$media) {
                return response()->json([
                    'message' => 'Image not found.',
                    'code' => 404,
                ], 404);
            }

            $media->delete();

            return response()->json([
                'message' => 'Image removed successfully.',
                'data' => null,
                'code' => 200,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product detail not found.',
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to remove image.',
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }
}
