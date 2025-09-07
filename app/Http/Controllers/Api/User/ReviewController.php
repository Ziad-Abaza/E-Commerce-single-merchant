<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Http\Resources\ReviewResource;
use App\Http\Requests\ReviewStoreRequest;
use App\Http\Requests\ReviewUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Display a listing of the reviews.
     */
    public function index(Request $request)
    {
        try {
            $query = Review::with(['user', 'product']);

            // Filter by product if specified
            if ($request->has('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            // Filter by user if specified
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Filter by rating if specified
            if ($request->has('rating')) {
                $query->where('rating', $request->rating);
            }

            // Filter by minimum rating if specified
            if ($request->has('min_rating')) {
                $query->where('rating', '>=', $request->min_rating);
            }

            // Filter by approval status if specified
            if ($request->has('is_approved')) {
                $query->where('is_approved', $request->boolean('is_approved'));
            }

            // Filter by verified purchase if specified
            if ($request->has('is_verified_purchase')) {
                $query->where('is_verified_purchase', $request->boolean('is_verified_purchase'));
            }

            // Filter by date range if specified
            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $reviews = $query->orderBy('created_at', 'desc')->paginate(15);

            return response()->json([
                'message' => 'Reviews retrieved successfully.',
                'data' => ReviewResource::collection($reviews),
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'per_page' => $reviews->perPage(),
                    'total' => $reviews->total(),
                ],
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve reviews.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Display the specified review as an array (for consistency with index endpoint)
     */
    public function show($id)
    {
        try {
            $review = Review::with(['user', 'product'])->findOrFail($id);

            // Wrap single review in an array
            $reviews = [$review];

            return response()->json([
                'message' => 'Review retrieved successfully.',
                'data' => ReviewResource::collection($reviews),
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 1,
                    'total' => 1,
                ],
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Review not found.',
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 0,
                    'total' => 0,
                ],
                'errors' => ['review' => ['Review could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve review.',
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 0,
                    'total' => 0,
                ],
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }


    /**
     * Store a newly created review.
     */
    public function store(ReviewStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $review = Review::create($data);

            DB::commit();

            return response()->json([
                'message' => 'Review created successfully.',
                'data' => new ReviewResource($review->load(['user', 'product'])),
                'errors' => null,
                'code' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create review.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Update the specified review.
     */
    public function update(ReviewUpdateRequest $request, $id)
    {
        try {
            $review = Review::findOrFail($id);

            DB::beginTransaction();

            $data = $request->validated();
            $review->update($data);

            DB::commit();

            return response()->json([
                'message' => 'Review updated successfully.',
                'data' => new ReviewResource($review->fresh(['user', 'product'])),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Review not found.',
                'data' => null,
                'errors' => ['review' => ['Review could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update review.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Remove the specified review.
     */
    public function destroy($id)
    {
        try {
            $review = Review::findOrFail($id);
            $review->delete();

            return response()->json([
                'message' => 'Review deleted successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Review not found.',
                'data' => null,
                'errors' => ['review' => ['Review could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete review.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Approve the specified review.
     */
    public function approve($id)
    {
        try {
            $review = Review::findOrFail($id);

            if ($review->is_approved) {
                return response()->json([
                    'message' => 'Review is already approved.',
                    'data' => null,
                    'errors' => ['review' => ['Review status is already approved.']],
                    'code' => 400,
                ], 400);
            }

            $review->update(['is_approved' => true]);

            return response()->json([
                'message' => 'Review approved successfully.',
                'data' => new ReviewResource($review->fresh(['user', 'product'])),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Review not found.',
                'data' => null,
                'errors' => ['review' => ['Review could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to approve review.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Reject the specified review.
     */
    public function reject($id)
    {
        try {
            $review = Review::findOrFail($id);

            if (!$review->is_approved) {
                return response()->json([
                    'message' => 'Review is already rejected.',
                    'data' => null,
                    'errors' => ['review' => ['Review status is already rejected.']],
                    'code' => 400,
                ], 400);
            }

            $review->update(['is_approved' => false]);

            return response()->json([
                'message' => 'Review rejected successfully.',
                'data' => new ReviewResource($review->fresh(['user', 'product'])),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Review not found.',
                'data' => null,
                'errors' => ['review' => ['Review could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to reject review.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Get product rating statistics.
     */
    public function getProductRatingStats($productId)
    {
        try {
            $averageRating = Review::getAverageRating($productId);
            $ratingDistribution = Review::getRatingDistribution($productId);
            $totalReviews = Review::where('product_id', $productId)
                ->where('is_approved', true)
                ->count();

            return response()->json([
                'message' => 'Product rating statistics retrieved successfully.',
                'data' => [
                    'product_id' => $productId,
                    'average_rating' => round($averageRating, 2),
                    'total_reviews' => $totalReviews,
                    'rating_distribution' => $ratingDistribution,
                ],
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve product rating statistics.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }
}
