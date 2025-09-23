<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Get paginated list of categories with statistics.
     */
    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->get('per_page', 10);
            $perPage = max(1, min(100, $perPage));

            $allowedSort = ['created_at', 'updated_at', 'name', 'sort_order'];
            $sortBy = $request->get('sort_by', 'created_at');
            if (!in_array($sortBy, $allowedSort, true)) {
                $sortBy = 'created_at';
            }

            $sortDir = strtolower($request->get('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

            $query = Category::query()
                ->with('parent')
                ->withCount('products')
                ->orderBy($sortBy, $sortDir);

            // Search (name, slug, description)
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Status filter: only when provided and non-empty
            if ($request->filled('status')) {
                if ($request->status === 'active') {
                    $query->active();
                } elseif ($request->status === 'inactive') {
                    $query->inactive();
                }
            }

            // Parent filter:
            if ($request->has('parent_id')) {
                $parentId = $request->input('parent_id');
                if ($parentId === '' || strtolower($parentId) === 'root' || $parentId === 'null') {
                    $query->whereNull('parent_id');
                } else {
                    $query->where('parent_id', $parentId);
                }
            }

            $categories = $query->paginate($perPage);

            $stats = [
                'total'    => Category::count(),
                'active'   => Category::active()->count(),
                'inactive' => Category::inactive()->count(),
                'root'     => Category::root()->count(),
            ];

            return response()->json([
                'success'    => true,
                'message'    => 'Category list retrieved successfully.',
                'data'       => CategoryResource::collection($categories),
                'pagination' => [
                    'current_page' => $categories->currentPage(),
                    'per_page'     => $categories->perPage(),
                    'total'        => $categories->total(),
                    'last_page'    => $categories->lastPage(),
                    'from'         => $categories->firstItem(),
                    'to'           => $categories->lastItem(),
                ],
                'stats'      => $stats,
                'errors'     => null,
                'code'       => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories.',
                'data'    => null,
                'errors'  => ['server' => [$e->getMessage()]],
                'code'    => 500,
            ], 500);
        }
    }

    /**
     * Show single category.
     */
    public function show($id)
    {
        try {
            $category = Category::with(['parent', 'children'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Category retrieved successfully.',
                'data'    => new CategoryResource($category),
                'errors'  => null,
                'code'    => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
                'data'    => null,
                'errors'  => ['category' => ['Category could not be found.']],
                'code'    => 404,
            ], 404);
        }
    }

    /**
     * Store new category.
     */
    public function store(CategoryStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $data['is_active'] = $data['is_active'] ?? true;

            DB::beginTransaction();
            $category = Category::create($data);
            DB::commit();

            if ($request->hasFile('thumbnail')) {
                $category->setThumbnail($request->file('thumbnail'));
            }
            if ($request->hasFile('icon')) {
                $category->setIcon($request->file('icon'));
            }

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully.',
                'data'    => new CategoryResource($category),
                'errors'  => null,
                'code'    => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category.',
                'data'    => null,
                'errors'  => ['server' => [$e->getMessage()]],
                'code'    => 500,
            ], 500);
        }
    }

    /**
     * Update category.
     */
    public function update(CategoryUpdateRequest $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            $data = $request->validated();

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
                'success' => true,
                'message' => 'Category updated successfully.',
                'data'    => new CategoryResource($category),
                'errors'  => null,
                'code'    => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
                'data'    => null,
                'errors'  => ['category' => ['Category could not be found.']],
                'code'    => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category.',
                'data'    => null,
                'errors'  => ['server' => [$e->getMessage()]],
                'code'    => 500,
            ], 500);
        }
    }

    /**
     * Delete category.
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            DB::beginTransaction();
            $category->delete();
            $category->setIcon(null);
            $category->setThumbnail(null);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.',
                'data'    => null,
                'errors'  => null,
                'code'    => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
                'data'    => null,
                'errors'  => ['category' => ['Category could not be found.']],
                'code'    => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category.',
                'data'    => null,
                'errors'  => ['server' => [$e->getMessage()]],
                'code'    => 500,
            ], 500);
        }
    }
}
