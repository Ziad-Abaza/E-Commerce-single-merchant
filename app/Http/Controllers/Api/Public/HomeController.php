<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    /**
     * Get all homepage data in one request
     */
    public function index(Request $request): JsonResponse
    {
        $data = [];

        // Featured Products
        $featuredProducts = Product::with(['categories', 'details', 'reviews'])
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(8)
            ->get();

        $data['featured_products'] = ProductResource::collection($featuredProducts);

        // Latest Products
        $latestProducts = Product::with(['categories', 'details', 'reviews'])
            ->where('is_active', true)
            ->latest()
            ->limit(12)
            ->get();

        $data['latest_products'] = ProductResource::collection($latestProducts);

        // Categories
        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        $data['categories'] = CategoryResource::collection($categories);

        // Statistics
        $stats = [
            'total_products' => Product::where('is_active', true)->count(),
            'total_categories' => Category::where('is_active', true)->count(),
            'total_orders' => \App\Models\Order::count(),
            'total_customers' => \App\Models\User::whereHas('roles', function($q) {
                $q->where('name', 'customer');
            })->count(),
            'featured_products_count' => $featuredProducts->count(),
            'latest_products_count' => $latestProducts->count(),
        ];

        $data['statistics'] = $stats;

        // Optional: Search if query param provided
        if ($request->filled('search')) {
            $searchResults = $this->performSearch($request);
            $data['search_results'] = $searchResults['data'];
            $data['pagination'] = $searchResults['pagination'] ?? null;
        }

        // Optional: Products by category if categoryId provided
        if ($request->filled('category_id')) {
            $categoryProducts = $this->getProductsByCategoryData($request->category_id, $request);
            $data['category_products'] = $categoryProducts['data'];
            $data['category'] = $categoryProducts['category'];
            $data['pagination'] = $categoryProducts['pagination'] ?? null;
        }

        return response()->json([
            'message' => 'Homepage data retrieved successfully.',
            'data' => $data,
            'code' => 200,
            'success' => true,
        ]);
    }

    /**
     * Helper: Reusable search logic
     */
    private function performSearch(Request $request)
    {
        $query = Product::with(['categories', 'details', 'reviews'])
            ->where('is_active', true);

        $query->where(function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        });

        if ($request->category_id) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        switch ($request->sort ?? 'created_at') {
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

        return [
            'data' => ProductResource::collection($products->items()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ];
    }

    /**
     * Helper: Get products by category
     */
    private function getProductsByCategoryData($categoryId, Request $request)
    {
        $category = Category::findOrFail($categoryId);

        $query = Product::with(['categories', 'details', 'reviews'])
            ->where('is_active', true)
            ->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });

        switch ($request->sort ?? 'created_at') {
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

        return [
            'category' => new CategoryResource($category),
            'data' => ProductResource::collection($products->items()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ];
    }
}
