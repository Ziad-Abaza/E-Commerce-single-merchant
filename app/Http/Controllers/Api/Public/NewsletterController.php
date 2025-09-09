<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class NewsletterController extends Controller
{
    /**
     * Subscribe to newsletter
     */
    public function subscribe(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email|max:255',
            ]);

            // Check if email already exists
            $existingSubscriber = NewsletterSubscriber::where('email', $request->email)->first();
            
            if ($existingSubscriber) {
                if ($existingSubscriber->is_active) {
                    return response()->json([
                        'message' => 'You are already subscribed to our newsletter.',
                        'success' => false,
                        'code' => 409,
                    ], 409);
                } else {
                    // Reactivate existing subscription
                    $existingSubscriber->update(['is_active' => true]);
                    
                    return response()->json([
                        'message' => 'Successfully reactivated your newsletter subscription!',
                        'success' => true,
                        'code' => 200,
                    ]);
                }
            }

            // Create new subscription
            NewsletterSubscriber::create([
                'email' => $request->email,
                'is_active' => true,
                'subscribed_at' => now(),
            ]);

            return response()->json([
                'message' => 'Successfully subscribed to our newsletter!',
                'success' => true,
                'code' => 201,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Please provide a valid email address.',
                'errors' => $e->errors(),
                'success' => false,
                'code' => 422,
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while subscribing. Please try again.',
                'success' => false,
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email|max:255',
            ]);

            $subscriber = NewsletterSubscriber::where('email', $request->email)->first();
            
            if (!$subscriber) {
                return response()->json([
                    'message' => 'Email address not found in our newsletter list.',
                    'success' => false,
                    'code' => 404,
                ], 404);
            }

            $subscriber->update(['is_active' => false]);

            return response()->json([
                'message' => 'Successfully unsubscribed from our newsletter.',
                'success' => true,
                'code' => 200,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Please provide a valid email address.',
                'errors' => $e->errors(),
                'success' => false,
                'code' => 422,
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while unsubscribing. Please try again.',
                'success' => false,
                'code' => 500,
            ], 500);
        }
    }
}
