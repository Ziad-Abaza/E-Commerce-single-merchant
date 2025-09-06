<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use App\Http\Resources\ReviewResource;
use App\Http\Requests\ReviewUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'user_id' => 'nullable|exists:users,id',
            'rating' => 'nullable|integer|min:1|max:5',
            'status' => 'nullable|in:pending,approved,rejected',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort' => 'nullable|in:created_at,updated_at,rating,helpful_count',
            'order' => 'nullable|in:asc,desc',
        ]);

        $query = Review::with(['user', 'product']);

        // Filter by product
        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by user
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by rating
        if ($request->rating) {
            $query->where('rating', $request->rating);
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Apply sorting
        $sortField = $request->sort ?? 'created_at';
        $sortOrder = $request->order ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        $perPage = $request->per_page ?? 15;
        $reviews = $query->paginate($perPage);

        return response()->json([
            'message' => 'Reviews retrieved successfully.',
            'data' => ReviewResource::collection($reviews->items()),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
            'code' => 200,
        ]);
    }

    /**
     * Display the specified review
     */
    public function show($id): JsonResponse
    {
        $review = Review::with(['user', 'product'])
            ->findOrFail($id);

        return response()->json([
            'message' => 'Review retrieved successfully.',
            'data' => new ReviewResource($review),
            'code' => 200,
        ]);
    }

    /**
     * Update the specified review
     */
    public function update(ReviewUpdateRequest $request, $id): JsonResponse
    {
        $review = Review::findOrFail($id);

        $validated = $request->validated();
        $review->update($validated);

        return response()->json([
            'message' => 'Review updated successfully.',
            'data' => new ReviewResource($review->fresh(['user', 'product'])),
            'code' => 200,
        ]);
    }

    /**
     * Approve a review
     */
    public function approve($id): JsonResponse
    {
        $review = Review::findOrFail($id);
        
        $review->update(['status' => 'approved']);

        // Update product average rating
        $this->updateProductRating($review->product_id);

        return response()->json([
            'message' => 'Review approved successfully.',
            'data' => new ReviewResource($review->fresh(['user', 'product'])),
            'code' => 200,
        ]);
    }

    /**
     * Reject a review
     */
    public function reject(Request $request, $id): JsonResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        $review = Review::findOrFail($id);
        
        $review->update([
            'status' => 'rejected',
            'admin_notes' => $request->reason,
        ]);

        // Update product average rating
        $this->updateProductRating($review->product_id);

        return response()->json([
            'message' => 'Review rejected successfully.',
            'data' => new ReviewResource($review->fresh(['user', 'product'])),
            'code' => 200,
        ]);
    }

    /**
     * Bulk approve reviews
     */
    public function bulkApprove(Request $request): JsonResponse
    {
        $request->validate([
            'review_ids' => 'required|array|min:1',
            'review_ids.*' => 'exists:reviews,id',
        ]);

        $reviews = Review::whereIn('id', $request->review_ids)
            ->where('status', 'pending')
            ->get();

        $productIds = $reviews->pluck('product_id')->unique();

        Review::whereIn('id', $request->review_ids)
            ->update(['status' => 'approved']);

        // Update product ratings
        foreach ($productIds as $productId) {
            $this->updateProductRating($productId);
        }

        return response()->json([
            'message' => 'Reviews approved successfully.',
            'data' => [
                'approved_count' => $reviews->count(),
            ],
            'code' => 200,
        ]);
    }

    /**
     * Bulk reject reviews
     */
    public function bulkReject(Request $request): JsonResponse
    {
        $request->validate([
            'review_ids' => 'required|array|min:1',
            'review_ids.*' => 'exists:reviews,id',
            'reason' => 'nullable|string|max:1000',
        ]);

        $reviews = Review::whereIn('id', $request->review_ids)
            ->where('status', 'pending')
            ->get();

        $productIds = $reviews->pluck('product_id')->unique();

        Review::whereIn('id', $request->review_ids)
            ->update([
                'status' => 'rejected',
                'admin_notes' => $request->reason,
            ]);

        // Update product ratings
        foreach ($productIds as $productId) {
            $this->updateProductRating($productId);
        }

        return response()->json([
            'message' => 'Reviews rejected successfully.',
            'data' => [
                'rejected_count' => $reviews->count(),
            ],
            'code' => 200,
        ]);
    }

    /**
     * Delete a review
     */
    public function destroy($id): JsonResponse
    {
        $review = Review::findOrFail($id);
        $productId = $review->product_id;
        
        $review->delete();

        // Update product average rating
        $this->updateProductRating($productId);

        return response()->json([
            'message' => 'Review deleted successfully.',
            'code' => 200,
        ]);
    }

    /**
     * Get review statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_reviews' => Review::count(),
            'pending_reviews' => Review::where('status', 'pending')->count(),
            'approved_reviews' => Review::where('status', 'approved')->count(),
            'rejected_reviews' => Review::where('status', 'rejected')->count(),
            'average_rating' => Review::where('status', 'approved')->avg('rating'),
            'rating_distribution' => Review::where('status', 'approved')
                ->select('rating', DB::raw('COUNT(*) as count'))
                ->groupBy('rating')
                ->orderBy('rating')
                ->get(),
        ];

        return response()->json([
            'message' => 'Review statistics retrieved successfully.',
            'data' => $stats,
            'code' => 200,
        ]);
    }

    /**
     * Get pending reviews
     */
    public function pending(): JsonResponse
    {
        $reviews = Review::with(['user', 'product'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);

        return response()->json([
            'message' => 'Pending reviews retrieved successfully.',
            'data' => ReviewResource::collection($reviews->items()),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
            'code' => 200,
        ]);
    }

    /**
     * Get reviews by product
     */
    public function byProduct($productId): JsonResponse
    {
        $product = Product::findOrFail($productId);
        
        $reviews = Review::with(['user'])
            ->where('product_id', $productId)
            ->where('status', 'approved')
            ->latest()
            ->paginate(15);

        return response()->json([
            'message' => "Reviews for product '{$product->name}' retrieved successfully.",
            'product' => $product,
            'data' => ReviewResource::collection($reviews->items()),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
            'code' => 200,
        ]);
    }

    /**
     * Get reviews by rating
     */
    public function byRating($rating): JsonResponse
    {
        if (!in_array($rating, [1, 2, 3, 4, 5])) {
            return response()->json([
                'message' => 'Invalid rating provided.',
                'code' => 400,
            ], 400);
        }

        $reviews = Review::with(['user', 'product'])
            ->where('rating', $rating)
            ->where('status', 'approved')
            ->latest()
            ->paginate(15);

        return response()->json([
            'message' => "Reviews with {$rating} star rating retrieved successfully.",
            'data' => ReviewResource::collection($reviews->items()),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
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
                ->where('status', 'approved')
                ->avg('rating');
            
            $product->update(['average_rating' => round($averageRating, 2)]);
        }
    }
}
