<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $users = User::where('is_active', true)->get();

        if ($users->isEmpty()) {
            $this->command->warn('No active users found. Please run UserSeeder first.');
            return;
        }

        foreach ($products as $product) {
            $this->createReviewsForProduct($product, $users);
        }
    }

    private function createReviewsForProduct(Product $product, $users): void
    {
        $reviewCount = rand(3, 15); // Random number of reviews per product
        $usedUsers = $users->random(min($reviewCount, $users->count()));

        foreach ($usedUsers as $user) {
            $rating = $this->generateRating();
            $reviewData = $this->generateReviewContent($product, $rating);

            Review::create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'rating' => $rating,
                'title' => $reviewData['title'],
                'comment' => $reviewData['comment'],
                'is_verified_purchase' => rand(0, 1) == 1,
                'active' => rand(0, 10) > 1, // 90% active rate
            ]);
        }
    }

    private function generateRating(): int
    {
        // Weighted random rating (more 4-5 star reviews)
        $weights = [1 => 5, 2 => 10, 3 => 20, 4 => 35, 5 => 30];
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($weights as $rating => $weight) {
            $currentWeight += $weight;
            if ($random <= $currentWeight) {
                return $rating;
            }
        }
        
        return 5; // Fallback
    }

    private function generateReviewContent(Product $product, int $rating): array
    {
        $productName = $product->name;
        $productCategory = $product->categories->first()?->name ?? 'product';

        $positiveTitles = [
            "Excellent {$productName}!",
            "Love this {$productName}",
            "Great quality and value",
            "Perfect for my needs",
            "Highly recommend!",
            "Outstanding product",
            "Exactly what I wanted",
            "Worth every penny",
            "Amazing quality",
            "Best purchase ever!",
        ];

        $neutralTitles = [
            "Good {$productName}",
            "Decent product",
            "Meets expectations",
            "Average quality",
            "Okay for the price",
            "Not bad",
            "Does the job",
            "Fair product",
            "Acceptable quality",
        ];

        $negativeTitles = [
            "Disappointed with {$productName}",
            "Not what I expected",
            "Poor quality",
            "Waste of money",
            "Would not recommend",
            "Defective product",
            "Cheaply made",
            "Doesn't work as advertised",
            "Overpriced",
        ];

        $positiveComments = [
            "This {$productName} exceeded my expectations. The quality is outstanding and it works perfectly. I would definitely buy again and recommend to others.",
            "I'm very happy with this purchase. The {$productName} is well-made and performs exactly as described. Fast shipping too!",
            "Excellent product! The quality is top-notch and it arrived quickly. This {$productName} is definitely worth the money.",
            "Love this {$productName}! It's exactly what I was looking for. Great quality and fast delivery. Highly recommend!",
            "Outstanding quality and value. This {$productName} is perfect for my needs. I'm very satisfied with my purchase.",
            "This is a fantastic {$productName}. The build quality is excellent and it works flawlessly. Great customer service too!",
            "Perfect! This {$productName} is exactly what I needed. High quality and great value. Will definitely buy again.",
            "Amazing product! The {$productName} is well-designed and works perfectly. I'm very impressed with the quality.",
            "Great purchase! This {$productName} is excellent quality and arrived quickly. I would definitely recommend it.",
            "This {$productName} is outstanding! Great quality, fast shipping, and excellent value. I'm very happy with my purchase.",
        ];

        $neutralComments = [
            "This {$productName} is okay. It works as expected but nothing extraordinary. Decent quality for the price.",
            "The {$productName} is fine. It does what it's supposed to do but I expected a bit more quality.",
            "Average product. It works but the quality could be better. It's okay for the price point.",
            "This {$productName} is decent. It meets my basic needs but I've seen better quality products.",
            "It's an okay {$productName}. Does the job but nothing special. Average quality overall.",
            "The {$productName} is acceptable. It works but I expected better quality for this price range.",
            "Decent product overall. It works as advertised but the quality is just average.",
            "This {$productName} is fine. It does what it needs to do but could be better quality.",
            "Average quality {$productName}. It works but nothing exceptional about it.",
        ];

        $negativeComments = [
            "I'm disappointed with this {$productName}. The quality is poor and it doesn't work as advertised. Would not recommend.",
            "This {$productName} is not worth the money. Poor quality and doesn't function properly. Very disappointed.",
            "Cheaply made {$productName}. It broke after just a few uses. Waste of money in my opinion.",
            "Terrible quality. This {$productName} doesn't work as described and feels very cheap. Would not buy again.",
            "I expected much better quality for this price. The {$productName} is poorly made and doesn't function well.",
            "This {$productName} is defective. It doesn't work properly and the quality is very poor. Very disappointed.",
            "Overpriced for the quality. This {$productName} is cheaply made and doesn't meet expectations.",
            "Poor quality {$productName}. It broke quickly and doesn't work as advertised. Not recommended.",
            "This {$productName} is a waste of money. Poor quality and doesn't function properly. Very disappointed.",
        ];

        // Category-specific comments
        $categoryComments = $this->getCategorySpecificComments($productCategory, $rating);

        if ($rating >= 4) {
            $title = $positiveTitles[array_rand($positiveTitles)];
            $comment = $categoryComments ? $categoryComments[array_rand($categoryComments)] : $positiveComments[array_rand($positiveComments)];
        } elseif ($rating == 3) {
            $title = $neutralTitles[array_rand($neutralTitles)];
            $comment = $categoryComments ? $categoryComments[array_rand($categoryComments)] : $neutralComments[array_rand($neutralComments)];
        } else {
            $title = $negativeTitles[array_rand($negativeTitles)];
            $comment = $categoryComments ? $categoryComments[array_rand($categoryComments)] : $negativeComments[array_rand($negativeComments)];
        }

        return [
            'title' => $title,
            'comment' => $comment,
        ];
    }

    private function getCategorySpecificComments(string $category, int $rating): ?array
    {
        $categoryComments = [
            'Electronics' => [
                'positive' => [
                    "Great electronic device! The build quality is excellent and all features work perfectly. Battery life is impressive too.",
                    "Outstanding electronics product. The performance is top-notch and the user interface is intuitive. Highly recommend!",
                    "Excellent electronic device. Fast, reliable, and well-designed. This is exactly what I was looking for.",
                    "Perfect electronics purchase! The quality is outstanding and it works flawlessly. Great value for money.",
                ],
                'neutral' => [
                    "Decent electronic device. It works as expected but the battery life could be better. Average quality overall.",
                    "Okay electronics product. It functions properly but nothing exceptional about the performance or features.",
                    "Average electronic device. It does what it's supposed to do but I expected better quality for this price.",
                ],
                'negative' => [
                    "Poor electronic device. It doesn't work properly and the build quality is cheap. Very disappointed.",
                    "Terrible electronics product. It's defective and doesn't function as advertised. Waste of money.",
                ],
            ],
            'Clothing' => [
                'positive' => [
                    "Perfect fit and excellent quality! The fabric is comfortable and the sizing is accurate. Love this clothing item.",
                    "Great clothing piece! The material is high quality and it looks exactly like the photos. Very satisfied.",
                    "Excellent clothing item. The fit is perfect and the quality is outstanding. Would definitely buy again.",
                    "Love this clothing! The style is great and the quality is top-notch. Perfect for my wardrobe.",
                ],
                'neutral' => [
                    "Decent clothing item. The fit is okay but the quality could be better. Average for the price.",
                    "Okay clothing piece. It fits reasonably well but nothing special about the quality or style.",
                    "Average clothing item. It's wearable but the quality is just okay for this price range.",
                ],
                'negative' => [
                    "Poor quality clothing. The fabric is cheap and it doesn't fit well. Very disappointed with this purchase.",
                    "Terrible clothing item. The sizing is completely wrong and the quality is very poor. Would not recommend.",
                ],
            ],
            'Home' => [
                'positive' => [
                    "Perfect home item! The quality is excellent and it looks great in my space. Very satisfied with this purchase.",
                    "Great home product! It's well-made and fits perfectly in my home. Excellent value for money.",
                    "Love this home item! The quality is outstanding and it's exactly what I needed for my space.",
                    "Excellent home product. The build quality is top-notch and it looks fantastic. Highly recommend!",
                ],
                'neutral' => [
                    "Decent home item. It works okay but the quality could be better. Average for the price point.",
                    "Okay home product. It serves its purpose but nothing exceptional about the quality or design.",
                    "Average home item. It does what it needs to do but the quality is just okay.",
                ],
                'negative' => [
                    "Poor quality home item. It doesn't work properly and the build quality is cheap. Very disappointed.",
                    "Terrible home product. It's defective and doesn't function as advertised. Waste of money.",
                ],
            ],
            'Sports' => [
                'positive' => [
                    "Excellent sports equipment! The quality is outstanding and it performs perfectly for my workouts.",
                    "Great sports gear! It's durable and well-designed. Perfect for my fitness routine.",
                    "Love this sports equipment! The quality is top-notch and it works exactly as expected.",
                    "Perfect sports gear! It's well-made and performs excellently. Great value for money.",
                ],
                'neutral' => [
                    "Decent sports equipment. It works okay but the quality could be better. Average for the price.",
                    "Okay sports gear. It functions properly but nothing exceptional about the performance or durability.",
                    "Average sports equipment. It does the job but the quality is just okay.",
                ],
                'negative' => [
                    "Poor quality sports equipment. It doesn't work properly and feels cheap. Very disappointed.",
                    "Terrible sports gear. It's not durable and doesn't perform as advertised. Waste of money.",
                ],
            ],
        ];

        $categoryKey = $this->getCategoryKey($category);
        if (isset($categoryComments[$categoryKey])) {
            $ratingKey = $rating >= 4 ? 'positive' : ($rating == 3 ? 'neutral' : 'negative');
            return $categoryComments[$categoryKey][$ratingKey] ?? null;
        }

        return null;
    }

    private function getCategoryKey(string $category): string
    {
        if (str_contains($category, 'Electronics') || str_contains($category, 'Smartphones') || str_contains($category, 'Laptops') || str_contains($category, 'Audio') || str_contains($category, 'Cameras') || str_contains($category, 'Gaming')) {
            return 'Electronics';
        }
        if (str_contains($category, 'Clothing') || str_contains($category, 'Fashion') || str_contains($category, 'Shoes')) {
            return 'Clothing';
        }
        if (str_contains($category, 'Home') || str_contains($category, 'Garden') || str_contains($category, 'Furniture') || str_contains($category, 'Kitchen')) {
            return 'Home';
        }
        if (str_contains($category, 'Sports') || str_contains($category, 'Fitness') || str_contains($category, 'Outdoor')) {
            return 'Sports';
        }

        return 'Electronics'; // Default fallback
    }
}
