<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return response()->json([
            'message' => 'Categories retrieved successfully.',
            'data' => CategoryResource::collection($categories),
            'code' => 200,
            'success' => true
        ]);
    }

    /**
     * Display the specified category
     */
    public function show($id): JsonResponse
    {
        $category = Category::withCount('products')
            ->where('is_active', true)
            ->findOrFail($id);

        return response()->json([
            'message' => 'Category retrieved successfully.',
            'data' => new CategoryResource($category),
            'code' => 200,
            'success' => true
        ]);
    }

    /**
     * Get category with its products
     */
    public function withProducts(Request $request, $id): JsonResponse
    {
        $request->validate([
            'sort' => 'nullable|in:name,price_asc,price_desc,created_at,rating',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $category = Category::withCount('products')
            ->where('is_active', true)
            ->findOrFail($id);

        $query = $category->products()
            ->with(['categories', 'details', 'reviews'])
            ->where('is_active', true);

        // Apply sorting
        switch ($request->sort) {
            case 'name':
                $query->orderBy('name');
                break;
            case 'price_asc':
                $query->orderBy('price');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->withAvg('reviews', 'rating')
                    ->orderBy('reviews_avg_rating', 'desc');
                break;
            case 'created_at':
            default:
                $query->latest();
                break;
        }

        $perPage = $request->per_page ?? 12;
        $products = $query->paginate($perPage);

        return response()->json([
            'message' => "Category '{$category->name}' with products retrieved successfully.",
            'category' => new CategoryResource($category),
            'products' => $products,
            'code' => 200,
            'success' => true
        ]);
    }

    /**
     * Get parent categories (categories without parent)
     */
    public function parents(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return response()->json([
            'message' => 'Parent categories retrieved successfully.',
            'data' => CategoryResource::collection($categories),
            'code' => 200,
            'success' => true
        ]);
    }

    /**
     * Get subcategories of a specific category
     */
    public function subcategories($id): JsonResponse
    {
        $parentCategory = Category::findOrFail($id);

        $subcategories = Category::where('is_active', true)
            ->where('parent_id', $id)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return response()->json([
            'message' => "Subcategories of '{$parentCategory->name}' retrieved successfully.",
            'parent_category' => new CategoryResource($parentCategory),
            'data' => CategoryResource::collection($subcategories),
            'code' => 200,
            'success' => true
        ]);
    }

    /**
     * Get category tree (hierarchical structure)
     */
    public function tree(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->where('is_active', true)
                    ->withCount('products')
                    ->orderBy('name');
            }])
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return response()->json([
            'message' => 'Category tree retrieved successfully.',
            'data' => CategoryResource::collection($categories),
            'code' => 200,
            'success' => true
        ]);
    }
}
