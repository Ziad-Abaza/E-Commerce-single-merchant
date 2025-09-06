<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Http\Resources\CartResource;
use App\Http\Requests\CartStoreRequest;
use App\Http\Requests\CartUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::all();
        return response()->json([
            'message' => 'Cart list retrieved successfully.',
            'data' => CartResource::collection($carts),
            'errors' => null,
            'code' => 200,
        ], 200);
    }

    public function show($id)
    {
        try {
            $cart = Cart::findOrFail($id);
            return response()->json([
                'message' => 'Cart retrieved successfully.',
                'data' => new CartResource($cart),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Cart not found.',
                'data' => null,
                'errors' => ['cart' => ['Cart could not be found.']],
                'code' => 404,
            ], 404);
        }
    }

    public function store(CartStoreRequest $request)
    {
        try {
            $data = $request->validated();
            DB::beginTransaction();
            $cart = Cart::create($data);
            DB::commit();
            return response()->json([
                'message' => 'Cart created successfully.',
                'data' => new CartResource($cart),
                'errors' => null,
                'code' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create cart.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    public function update(CartUpdateRequest $request, $id)
    {
        try {
            $cart = Cart::findOrFail($id);
            $data = $request->validated();
            DB::beginTransaction();
            $cart->update($data);
            DB::commit();
            return response()->json([
                'message' => 'Cart updated successfully.',
                'data' => new CartResource($cart),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Cart not found.',
                'data' => null,
                'errors' => ['cart' => ['Cart could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update cart.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $cart = Cart::findOrFail($id);
            $cart->delete();
            return response()->json([
                'message' => 'Cart deleted successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Cart not found.',
                'data' => null,
                'errors' => ['cart' => ['Cart could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete cart.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }
} 