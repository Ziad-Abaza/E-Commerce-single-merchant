<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WishlistCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WishlistCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // The model's booted method will handle creating the default 'Favorites' category
            // if it doesn't exist when we save any wishlist category for the user
            WishlistCategory::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'is_default' => true,
                    'name' => 'Favorites'
                ],
                [
                    'is_default' => true
                ]
            );
        }
    }
}
