<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json([
            'message' => 'Category list retrieved successfully.',
            'data' => CategoryResource::collection($categories),
            'errors' => null,
            'code' => 200,
        ], 200);
    }

    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            return response()->json([
                'message' => 'Category retrieved successfully.',
                'data' => new CategoryResource($category),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Category not found.',
                'data' => null,
                'errors' => ['category' => ['Category could not be found.']],
                'code' => 404,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:categories,slug',
                'parent_id' => 'nullable|exists:categories,id',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
                'sort_order' => 'nullable|integer',
                'thumbnail' => 'nullable|image|max:8192',
                'icon' => 'nullable|image|max:8192',
            ]);

            $data['is_active'] = $data['is_active'] ?? true;
            DB::beginTransaction();
            $category = Category::create($data);
            DB::commit();

            if($request->hasFile('thumbnail')){
                $category->setThumbnail($request->file('thumbnail'));
            }
            if($request->hasFile('icon')){
                $category->setIcon($request->file('icon'));
            }

            return response()->json([
                'message' => 'Category created successfully.',
                'data' => new CategoryResource($category),
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
                'message' => 'Failed to create category.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            $data = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'slug' => 'sometimes|required|string|max:255|unique:categories,slug,' . $id,
                'parent_id' => 'nullable|exists:categories,id',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer',
                'thumbnail' => 'nullable|image|max:8192',
                'icon' => 'nullable|image|max:8192',
            ]);
            DB::beginTransaction();
            $category->update($data);
            DB::commit();

            if ($request->hasFile('thumbnail')) {
                $category->setThumbnail($request->file('thumbnail'));
            }
            if ($request->hasFile('icon')) {
                $category->setIcon($request->file('icon'));
            }

            return response()->json([
                'message' => 'Category updated successfully.',
                'data' => new CategoryResource($category),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Category not found.',
                'data' => null,
                'errors' => ['category' => ['Category could not be found.']],
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
                'message' => 'Failed to update category.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();
            $category->setIcon(null);
            $category->setThumbnail(null);
            return response()->json([
                'message' => 'Category deleted successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Category not found.',
                'data' => null,
                'errors' => ['category' => ['Category could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete category.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }
}
