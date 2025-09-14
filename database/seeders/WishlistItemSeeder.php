<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\WishlistCategory;
use App\Models\WishlistItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WishlistItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to avoid constraint violations
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        WishlistItem::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $wishlistCategories = WishlistCategory::with('user')->get();
        
        if ($wishlistCategories->isEmpty()) {
            $this->command->info('No wishlist categories found. Please run WishlistCategorySeeder first.');
            return;
        }

        $products = Product::pluck('id')->toArray();
        
        if (empty($products)) {
            $this->command->info('No products found. Please run ProductSeeder first.');
            return;
        }

        $itemsToCreate = [];
        
        foreach ($wishlistCategories as $category) {
            // Each category will have 1-5 random products
            $productCount = rand(1, min(5, count($products)));
            $randomProductIds = array_rand(array_flip($products), $productCount);
            
            if (!is_array($randomProductIds)) {
                $randomProductIds = [$randomProductIds];
            }
            
            foreach ($randomProductIds as $productId) {
                $itemsToCreate[] = [
                    'wishlist_category_id' => $category->id,
                    'product_id' => $productId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // Insert in chunks of 100 for better performance
                if (count($itemsToCreate) >= 100) {
                    WishlistItem::insert($itemsToCreate);
                    $itemsToCreate = [];
                }
            }
        }
        
        // Insert any remaining items
        if (!empty($itemsToCreate)) {
            WishlistItem::insert($itemsToCreate);
        }
        
        $this->command->info('Wishlist items seeded successfully!');
    }
}
