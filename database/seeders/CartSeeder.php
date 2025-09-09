<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\ProductDetail;
use App\Models\User;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('is_active', true)->get();
        $productDetails = ProductDetail::where('is_active', true)->where('stock', '>', 0)->get();

        if ($users->isEmpty() || $productDetails->isEmpty()) {
            $this->command->warn('No users or product details found. Please run UserSeeder and ProductDetailSeeder first.');
            return;
        }

        // Create cart items for some users (not all users have cart items)
        $userCount = $users->count();
        $cartUserCount = rand(4, min(15, $userCount));
        $usersWithCart = $users->random($cartUserCount);

        foreach ($usersWithCart as $user) {
            $this->createCartForUser($user, $productDetails);
        }
    }

    private function createCartForUser(User $user, $productDetails): void
    {
        // Random number of items in cart (1-5 items, but not more than available)
        $itemCount = rand(1, min(5, $productDetails->count()));
        $selectedProducts = $productDetails->random($itemCount);

        foreach ($selectedProducts as $productDetail) {
            $quantity = rand(1, 3);

            Cart::create([
                'user_id' => $user->id,
                'product_detail_id' => $productDetail->id,
                'quantity' => $quantity,
            ]);
        }
    }
}
