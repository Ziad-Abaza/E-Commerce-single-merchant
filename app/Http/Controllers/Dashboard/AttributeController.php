<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Http\Resources\AttributeResource;
use App\Http\Requests\AttributeStoreRequest;
use App\Http\Requests\AttributeUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class AttributeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->get('per_page', 15);
            $perPage = max(1, min(100, $perPage));

            $query = Attribute::query();

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('is_filterable')) {
                $query->where('is_filterable', (bool) $request->is_filterable);
            }

            if ($request->filled('is_variant')) {
                $query->where('is_variant', (bool) $request->is_variant);
            }

            $attributes = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Attributes retrieved successfully.',
                'data' => AttributeResource::collection($attributes->load('categories')),
                'pagination' => [
                    'current_page' => $attributes->currentPage(),
                    'per_page' => $attributes->perPage(),
                    'total' => $attributes->total(),
                    'last_page' => $attributes->lastPage(),
                    'from' => $attributes->firstItem(),
                    'to' => $attributes->lastItem(),
                ],
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve attributes.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $attribute = Attribute::with('categories')->findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Attribute retrieved successfully.',
                'data' => new AttributeResource($attribute),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Attribute not found.',
                'data' => null,
                'errors' => ['attribute' => ['Attribute could not be found.']],
                'code' => 404,
            ], 404);
        }
    }

    public function store(AttributeStoreRequest $request)
    {
        try {
            $data = $request->validated();

            DB::beginTransaction();
            $attribute = Attribute::create($data);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Attribute created successfully.',
                'data' => new AttributeResource($attribute->load('categories')),
                'errors' => null,
                'code' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create attribute.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    public function update(AttributeUpdateRequest $request, $id)
    {
        try {
            $attribute = Attribute::findOrFail($id);
            $data = $request->validated();

            DB::beginTransaction();
            $attribute->update($data);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Attribute updated successfully.',
                'data' => new AttributeResource($attribute->load('categories')),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Attribute not found.',
                'data' => null,
                'errors' => ['attribute' => ['Attribute could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update attribute.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $attribute = Attribute::findOrFail($id);

            if ($attribute->categories()->exists() || $attribute->values()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete attribute because it is in use.',
                    'data' => null,
                    'errors' => ['attribute' => ['This attribute is linked to categories or values and cannot be deleted.']],
                    'code' => 409,
                ], 409);
            }

            DB::beginTransaction();
            $attribute->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Attribute deleted successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Attribute not found.',
                'data' => null,
                'errors' => ['attribute' => ['Attribute could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete attribute.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }


    public function getByCategory($categoryId)
    {
        try {
            $category = \App\Models\Category::findOrFail($categoryId);

            $attributes = $category->attributes()->get();

            return response()->json([
                'success' => true,
                'message' => 'Attributes retrieved successfully for the selected category.',
                'data' => AttributeResource::collection($attributes),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
                'data' => null,
                'errors' => ['category' => ['Category not found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve attributes for category.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }
}
