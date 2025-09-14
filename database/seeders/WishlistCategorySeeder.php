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
            if (!$user->wishlistCategories()->default()->exists()) {
                WishlistCategory::create([
                    'user_id' => $user->id,
                    'name' => 'Favorites',
                    'is_default' => true,
                ]);
            }
        }
    }
}
