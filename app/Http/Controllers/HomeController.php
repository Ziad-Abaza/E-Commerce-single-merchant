<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    /**
     * Get featured products for homepage
     */
    public function featuredProducts(): JsonResponse
    {
        $products = Product::with(['categories', 'details', 'reviews'])
            ->where('is_featured', true)
            ->where('is_active', true)
            ->latest()
            ->limit(8)
            ->get();

        return response()->json([
            'message' => 'Featured products retrieved successfully.',
            'data' => ProductResource::collection($products),
            'code' => 200,
        ]);
    }

    /**
     * Get latest products
     */
    public function latestProducts(): JsonResponse
    {
        $products = Product::with(['categories', 'details', 'reviews'])
            ->where('is_active', true)
            ->latest()
            ->limit(12)
            ->get();

        return response()->json([
            'message' => 'Latest products retrieved successfully.',
            'data' => ProductResource::collection($products),
            'code' => 200,
        ]);
    }

    /**
     * Get all categories for navigation
     */
    public function categories(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return response()->json([
            'message' => 'Categories retrieved successfully.',
            'data' => CategoryResource::collection($categories),
            'code' => 200,
        ]);
    }

    /**
     * Search products
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'sort' => 'nullable|in:name,price_asc,price_desc,created_at',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Product::with(['categories', 'details', 'reviews'])
            ->where('is_active', true);

        // Search by name or description
        $query->where(function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->query . '%')
              ->orWhere('description', 'like', '%' . $request->query . '%');
        });

        // Filter by category
        if ($request->category_id) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Filter by price range
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

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
            case 'created_at':
            default:
                $query->latest();
                break;
        }

        $perPage = $request->per_page ?? 12;
        $products = $query->paginate($perPage);

        return response()->json([
            'message' => 'Search results retrieved successfully.',
            'data' => ProductResource::collection($products->items()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
            'code' => 200,
        ]);
    }

    /**
     * Get products by category
     */
    public function productsByCategory(Request $request, $categoryId): JsonResponse
    {
        $request->validate([
            'sort' => 'nullable|in:name,price_asc,price_desc,created_at',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $category = Category::findOrFail($categoryId);

        $query = Product::with(['categories', 'details', 'reviews'])
            ->where('is_active', true)
            ->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });

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
            case 'created_at':
            default:
                $query->latest();
                break;
        }

        $perPage = $request->per_page ?? 12;
        $products = $query->paginate($perPage);

        return response()->json([
            'message' => "Products in category '{$category->name}' retrieved successfully.",
            'category' => new CategoryResource($category),
            'data' => ProductResource::collection($products->items()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
            'code' => 200,
        ]);
    }

    /**
     * Get single product details
     */
    public function showProduct($id): JsonResponse
    {
        $product = Product::with(['categories', 'details', 'reviews.user'])
            ->where('is_active', true)
            ->findOrFail($id);

        return response()->json([
            'message' => 'Product retrieved successfully.',
            'data' => new ProductResource($product),
            'code' => 200,
        ]);
    }

    /**
     * Get homepage statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_products' => Product::where('is_active', true)->count(),
            'total_categories' => Category::where('is_active', true)->count(),
            'featured_products' => Product::where('is_featured', true)->where('is_active', true)->count(),
        ];

        return response()->json([
            'message' => 'Statistics retrieved successfully.',
            'data' => $stats,
            'code' => 200,
        ]);
    }
}
