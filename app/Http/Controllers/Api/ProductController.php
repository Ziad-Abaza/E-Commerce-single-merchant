<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'sort' => 'nullable|in:name,price_asc,price_desc,created_at,rating',
            'per_page' => 'nullable|integer|min:1|max:100',
            'featured' => 'nullable|boolean',
            'search' => 'nullable|string|max:255',
        ]);

        $query = Product::with(['categories', 'details', 'reviews'])
            ->where('is_active', true);

        // Filter by category
        if ($request->category_id) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Filter by featured products
        if ($request->has('featured')) {
            $query->where('is_featured', $request->featured);
        }

        // Search by name or description
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
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
            'message' => 'Products retrieved successfully.',
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
     * Display the specified product
     */
    public function show($id): JsonResponse
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
     * Get related products
     */
    public function related($id): JsonResponse
    {
        $product = Product::findOrFail($id);
        
        $relatedProducts = Product::with(['categories', 'details', 'reviews'])
            ->where('is_active', true)
            ->where('id', '!=', $id)
            ->whereHas('categories', function ($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->limit(4)
            ->get();

        return response()->json([
            'message' => 'Related products retrieved successfully.',
            'data' => ProductResource::collection($relatedProducts),
            'code' => 200,
        ]);
    }

    /**
     * Get product reviews
     */
    public function reviews($id): JsonResponse
    {
        $product = Product::findOrFail($id);
        
        $reviews = $product->reviews()
            ->with('user')
            ->latest()
            ->paginate(10);

        return response()->json([
            'message' => 'Product reviews retrieved successfully.',
            'data' => $reviews,
            'code' => 200,
        ]);
    }

    /**
     * Get featured products
     */
    public function featured(): JsonResponse
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
    public function latest(): JsonResponse
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
     * Get products by category
     */
    public function byCategory(Request $request, $categoryId): JsonResponse
    {
        $request->validate([
            'sort' => 'nullable|in:name,price_asc,price_desc,created_at,rating',
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
}
