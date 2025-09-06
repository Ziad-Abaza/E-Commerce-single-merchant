<?php

namespace App\Http\Controllers\Api\Public;

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
                // Already ordered by latest by default
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
                // Default case - all products with optional filters
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

        // Apply price filters (works for all types)
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Apply sorting
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
     * Display the specified product with optional includes
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request, $id): JsonResponse
    {
        // Build the base query
        $query = Product::where('is_active', true);

        // Determine what to include based on 'include' parameter
        $includes = [];
        if ($request->has('include')) {
            $requestedIncludes = explode(',', $request->include);
            $validIncludes = ['related', 'reviews'];

            foreach ($requestedIncludes as $include) {
                if (in_array($include, $validIncludes)) {
                    $includes[] = $include;
                }
            }
        }

        // Always load basic relations
        $product = $query->with(['categories', 'details'])->findOrFail($id);

        // Load additional relations if requested
        if (in_array('reviews', $includes)) {
            $product->load('reviews.user');
        }

        $response = [
            'message' => 'Product retrieved successfully.',
            'data' => new ProductResource($product),
            'code' => 200,
            'success' => true,
        ];

        // Add related products if requested
        if (in_array('related', $includes)) {
            $relatedProducts = Product::with(['categories', 'details', 'reviews'])
                ->where('is_active', true)
                ->where('id', '!=', $id)
                ->whereHas('categories', function ($q) use ($product) {
                    $q->whereIn('categories.id', $product->categories->pluck('id'));
                })
                ->limit(4)
                ->get();

            $response['related_products'] = ProductResource::collection($relatedProducts);
        }

        // Add reviews if requested (and not already loaded above)
        if (in_array('reviews', $includes) && !isset($response['reviews'])) {
            $reviews = $product->reviews()
                ->with('user')
                ->latest()
                ->paginate(10);

            $response['reviews'] = $reviews;
        }

        return response()->json($response);
    }
}
