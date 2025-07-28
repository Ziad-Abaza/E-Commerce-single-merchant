<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Http\Resources\CartResource;
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

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|exists:users,id',
                'product_detail_id' => 'required|exists:product_details,id',
                'quantity' => 'required|integer|min:1',
            ]);
            DB::beginTransaction();
            $cart = Cart::create($data);
            DB::commit();
            return response()->json([
                'message' => 'Cart created successfully.',
                'data' => new CartResource($cart),
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
                'message' => 'Failed to create cart.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $cart = Cart::findOrFail($id);
            $data = $request->validate([
                'quantity' => 'sometimes|required|integer|min:1',
            ]);
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