<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductDetailsResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ReviewResource;

class ProductController extends Controller
{
    /**
     * Unified endpoint for all product listings
     * Handles: all products, featured, latest, by category, search
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'sort' => 'nullable|in:name,price_asc,price_desc,created_at,rating',
            'per_page' => 'nullable|integer|min:1|max:100',
            'type' => 'nullable|in:all,featured,latest,category,search',
            'search' => 'nullable|string|max:255',
            'page' => 'nullable|integer|min:1',
        ]);

        $query = Product::with(['categories', 'details', 'reviews'])
            ->where('is_active', true);

        // Handle different product types
        $type = $request->type ?? 'all';

        switch ($type) {
            case 'featured':
                $query->where('is_active', true)
                    ->latest()
                    ->limit(8)
                    ->get();
                break;

            case 'latest':
                $query->where('is_active', true)
                    ->latest()
                    ->limit(8)
                    ->get();
                break;

            case 'category':
                if ($request->category_id) {
                    $query->whereHas('categories', function ($q) use ($request) {
                        $q->where('categories.id', $request->category_id);
                    });
                }
                break;

            case 'search':
                if ($request->search) {
                    $query->where(function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('description', 'like', '%' . $request->search . '%');
                    });
                }
                break;

            case 'all':
            default:
                if ($request->category_id) {
                    $query->whereHas('categories', function ($q) use ($request) {
                        $q->where('categories.id', $request->category_id);
                    });
                }

                if ($request->search) {
                    $query->where(function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('description', 'like', '%' . $request->search . '%');
                    });
                }
                break;
        }

        // Apply price filters using ProductDetail
        $query->whereHas('details', function ($q) use ($request) {
            if ($request->min_price) {
                $q->whereRaw('(price - discount) >= ?', [$request->min_price]);
            }
            if ($request->max_price) {
                $q->whereRaw('(price - discount) <= ?', [$request->max_price]);
            }
        });

        // Apply sorting
        switch ($request->sort ?? 'created_at') {
            case 'name':
                $query->orderBy('name');
                break;

            case 'price_asc':
                $query->withMin('details', 'price')
                    ->orderBy('details_min_price', 'asc');
                break;

            case 'price_desc':
                $query->withMin('details', 'price')
                    ->orderBy('details_min_price', 'desc');
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

        // Set pagination
        $perPage = $request->per_page ?? 12;
        $products = $query->paginate($perPage);

        // Get category info if filtering by category
        $category = null;
        if ($request->category_id && in_array($type, ['category', 'all'])) {
            $category = Category::find($request->category_id);
        }

        return response()->json([
            'message' => $this->getSuccessMessage($type, $category),
            'data' => ProductResource::collection($products->items()),
            'category' => $category ? new CategoryResource($category) : null,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
            'code' => 200,
            'success' => true,
        ]);
    }

    /**
     * Get success message based on product type
     */
    private function getSuccessMessage(string $type, $category = null): string
    {
        switch ($type) {
            case 'featured':
                return 'Featured products retrieved successfully.';
            case 'latest':
                return 'Latest products retrieved successfully.';
            case 'category':
                return $category ? "Products in category '{$category->name}' retrieved successfully." : 'Products by category retrieved successfully.';
            case 'search':
                return 'Search results retrieved successfully.';
            default:
                return 'Products retrieved successfully.';
        }
    }

    /**
     * Display the specified product with comprehensive data and related products
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request, $id): JsonResponse
    {
        // Build the base query for active products
        $query = Product::where('is_active', true);

        // Always load essential relations
        $product = $query->with(['categories', 'details'])->findOrFail($id);

        $keywords = explode(' ', $product->name);
        $likeConditions = collect($keywords)
            ->filter(fn($word) => strlen($word) >= 3)
            ->map(fn($word) => "name LIKE '%" . addslashes($word) . "%'")
            ->implode(' OR ');

        // Always fetch related products (by shared categories)
        $relatedProducts = Product::with(['categories', 'details'])
            ->where('is_active', true)
            ->where('id', '!=', $id)
            ->when($product->categories->isNotEmpty(), function ($q) use ($product) {
                $q->whereHas('categories', function ($subQuery) use ($product) {
                    $subQuery->whereIn('categories.id', $product->categories->pluck('id'));
                });
            })
            ->when(!empty($likeConditions), function ($q) use ($likeConditions) {
                $q->orWhereRaw("({$likeConditions})");
            })
            ->limit(8)
            ->get();

        $response = [
            'message' => 'Product retrieved successfully.',
            'data' => [
                'product' => new ProductDetailsResource($product),
                'related_products' => ProductResource::collection($relatedProducts),
            ],
            'code' => 200,
            'success' => true,
        ];

        return response()->json($response);
    }
}
