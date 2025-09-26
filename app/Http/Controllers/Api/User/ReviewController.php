<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use App\Http\Resources\ReviewResource;
use App\Http\Requests\ReviewStoreRequest;
use App\Http\Requests\ReviewUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Events\NewReviewEvent;
use Illuminate\Support\Facades\DB;
use App\Notifications\owner\NewReviewNotification;

class ReviewController extends Controller
{
    /**
     * Display a listing of the reviews.
     */
    /**
     * Display a listing of reviews for a specific product.
     */
    public function index(Request $request, $productId)
    {
        try {
            $query = Review::with(['user'])
                ->where('product_id', $productId)
                ->active();

            // Filter by rating if specified
            if ($request->has('rating')) {
                $query->where('rating', $request->rating);
            }

            // Filter by minimum rating if specified
            if ($request->has('min_rating')) {
                $query->where('rating', '>=', $request->min_rating);
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
                'success' => true,
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
                'success' => false,
                'message' => 'Failed to retrieve reviews.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Display the specified review.
     */
    public function show($id)
    {
        try {
            $review = Review::with(['user', 'product'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Review retrieved successfully.',
                'data' => new ReviewResource($review),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found.',
                'data' => null,
                'errors' => ['review' => ['Review could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve review.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Store a newly created review for a specific product.
     * If user already reviewed this product, update the existing one.
     */
    public function store(ReviewStoreRequest $request, $productId)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            $data['product_id'] = $productId;
                $review = Review::create($data);
                $message = 'Review created successfully.';
                $code = 201;

            DB::commit();

            $owners = User::role('owner')->get();
            foreach ($owners as $owner) {
                $owner->notify(new NewReviewNotification($review));
            }

            // After sending notifications to owners
            NewReviewEvent::dispatch($review);


            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $review,
                'errors' => null,
                'code' => $code,
            ], $code);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process review.',
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
                'success' => true,
                'message' => 'Review updated successfully.',
                'data' => new ReviewResource($review->fresh(['user', 'product'])),
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found.',
                'data' => null,
                'errors' => ['review' => ['Review could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
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
                'success' => true,
                'message' => 'Review deleted successfully.',
                'data' => null,
                'errors' => null,
                'code' => 200,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found.',
                'data' => null,
                'errors' => ['review' => ['Review could not be found.']],
                'code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete review.',
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
                ->where('active', true)
                ->count();

            return response()->json([
                'success' => true,
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
                'success' => false,
                'message' => 'Failed to retrieve product rating statistics.',
                'data' => null,
                'errors' => ['server' => [$e->getMessage()]],
                'code' => 500,
            ], 500);
        }
    }
}
