<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use App\Http\Resources\ReviewResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews with dynamic filters, search, sorting, and statistics
     */
    public function index(Request $request): JsonResponse
    {
        // Validation
        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'user_id' => 'nullable|exists:users,id',
            'rating' => 'nullable|integer|min:1|max:5',
            'status' => 'nullable|in:inactive,active',
            'search' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort' => 'nullable|in:created_at,updated_at,rating',
            'order' => 'nullable|in:asc,desc',
        ]);

        $query = Review::with(['user', 'product']);

        // Dynamic filters
        if ($request->product_id) $query->where('product_id', $request->product_id);
        if ($request->user_id) $query->where('user_id', $request->user_id);
        if ($request->rating) $query->where('rating', $request->rating);

        if ($request->status) {
            if ($request->status === 'active') {
                $query->where('active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('active', false);
            }
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('comment', 'like', "%{$request->search}%");
            });
        }

        // Sorting
        $sortField = $request->sort ?? 'created_at';
        $sortOrder = $request->order ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        // Pagination
        $perPage = $request->per_page ?? 15;
        $reviews = $query->paginate($perPage);

        // Statistics
        $statistics = [
            'total_reviews' => Review::count(),
            'inactive_reviews' => Review::where('active', false)->count(),
            'active_reviews' => Review::where('active', true)->count(),
            'pending_reviews' => Review::where('active', false)->count(), // Alias for backward compatibility
            'average_rating' => Review::where('active', true)->avg('rating'),
            'rating_distribution' => Review::where('active', true)
                ->select('rating', DB::raw('COUNT(*) as count'))
                ->groupBy('rating')
                ->orderBy('rating', 'desc')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Reviews retrieved successfully.',
            'data' => ReviewResource::collection($reviews->items()),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
            'statistics' => $statistics,
            'code' => 200,
        ]);
    }

    /**
     * Display the specified review
     */
    public function show($id): JsonResponse
    {
        $review = Review::with(['user', 'product'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Review retrieved successfully.',
            'data' => new ReviewResource($review),
            'code' => 200,
        ]);
    }

    /**
     * Update the specified review
     */
    public function update(Request $request, $id): JsonResponse
    {
        $review = Review::findOrFail($id);

        $validated = $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string',
            'active' => 'nullable|boolean',
            'is_verified_purchase' => 'nullable|boolean',
        ]);

        $review->update($validated);
        $this->updateProductRating($review->product_id);

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully.',
            'data' => new ReviewResource($review->fresh(['user', 'product'])),
            'code' => 200,
        ]);
    }

    /**
     * Toggle active status of a review
     */
    public function toggleActive($id): JsonResponse
    {
        $review = Review::findOrFail($id);
        $review->update(['active' => !$review->active]);
        $this->updateProductRating($review->product_id);

        return response()->json([
            'success' => true,
            'message' => $review->active ? 'Review activated successfully.' : 'Review deactivated successfully.',
            'data' => new ReviewResource($review->fresh(['user', 'product'])),
            'code' => 200,
        ]);
    }

    /**
     * Delete a single review
     */
    public function destroy($id): JsonResponse
    {
        $review = Review::findOrFail($id);
        $productId = $review->product_id;

        $review->delete();
        $this->updateProductRating($productId);

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully.',
            'code' => 200,
        ]);
    }

    /**
     * Activate a review
     */
    public function activate($id): JsonResponse
    {
        $review = Review::findOrFail($id);
        $review->update(['active' => true]);
        $this->updateProductRating($review->product_id);

        return response()->json([
            'success' => true,
            'message' => 'Review activated successfully.',
            'code' => 200,
        ]);
    }

    /**
     * Deactivate a review
     */
    public function deactivate($id): JsonResponse
    {
        $review = Review::findOrFail($id);
        $review->update(['active' => false]);
        $this->updateProductRating($review->product_id);

        return response()->json([
            'success' => true,
            'message' => 'Review deactivated successfully.',
            'code' => 200,
        ]);
    }

    /**
     * Approve a review (alias for activate - backward compatibility)
     */
    public function approve($id): JsonResponse
    {
        return $this->activate($id);
    }

    /**
     * Reject a review (alias for deactivate - backward compatibility)
     */
    public function reject($id): JsonResponse
    {
        return $this->deactivate($id);
    }

    /**
     * Bulk activate reviews
     */
    public function bulkActivate(Request $request): JsonResponse
    {
        $request->validate([
            'review_ids' => 'required|array|min:1',
            'review_ids.*' => 'exists:reviews,id',
        ]);

        $reviews = Review::whereIn('id', $request->review_ids)->get();
        $productIds = $reviews->pluck('product_id')->unique();

        Review::whereIn('id', $request->review_ids)->update(['active' => true]);

        foreach ($productIds as $productId) {
            $this->updateProductRating($productId);
        }

        return response()->json([
            'success' => true,
            'message' => 'Selected reviews activated successfully.',
            'updated_count' => count($request->review_ids),
            'code' => 200,
        ]);
    }

    /**
     * Bulk deactivate reviews
     */
    public function bulkDeactivate(Request $request): JsonResponse
    {
        $request->validate([
            'review_ids' => 'required|array|min:1',
            'review_ids.*' => 'exists:reviews,id',
        ]);

        $reviews = Review::whereIn('id', $request->review_ids)->get();
        $productIds = $reviews->pluck('product_id')->unique();

        Review::whereIn('id', $request->review_ids)->update(['active' => false]);

        foreach ($productIds as $productId) {
            $this->updateProductRating($productId);
        }

        return response()->json([
            'success' => true,
            'message' => 'Selected reviews deactivated successfully.',
            'updated_count' => count($request->review_ids),
            'code' => 200,
        ]);
    }

    /**
     * Bulk approve reviews (alias for bulkActivate - backward compatibility)
     */
    public function bulkApprove(Request $request): JsonResponse
    {
        return $this->bulkActivate($request);
    }

    /**
     * Bulk reject reviews (alias for bulkDeactivate - backward compatibility)
     */
    public function bulkReject(Request $request): JsonResponse
    {
        return $this->bulkDeactivate($request);
    }

    /**
     * Bulk delete reviews
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $request->validate([
            'review_ids' => 'required|array|min:1',
            'review_ids.*' => 'exists:reviews,id',
        ]);

        $reviews = Review::whereIn('id', $request->review_ids)->get();
        $productIds = $reviews->pluck('product_id')->unique();

        Review::whereIn('id', $request->review_ids)->delete();

        foreach ($productIds as $productId) {
            $this->updateProductRating($productId);
        }

        return response()->json([
            'success' => true,
            'message' => 'Selected reviews deleted successfully.',
            'deleted_count' => count($request->review_ids),
            'code' => 200,
        ]);
    }

    /**
     * Update product average rating
     */
    private function updateProductRating($productId): void
    {
        $product = Product::find($productId);
        if ($product) {
            $averageRating = Review::where('product_id', $productId)
                ->where('active', true)
                ->avg('rating');
            $product->update(['average_rating' => round($averageRating, 2)]);
        }
    }
}
