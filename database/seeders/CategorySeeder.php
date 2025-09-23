<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Root Categories
        $electronics = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic devices and gadgets for modern living',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $clothing = Category::create([
            'name' => 'Clothing & Fashion',
            'slug' => 'clothing-fashion',
            'description' => 'Fashionable clothing for men, women, and children',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $homeGarden = Category::create([
            'name' => 'Home & Garden',
            'slug' => 'home-garden',
            'description' => 'Everything for your home and garden',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $sports = Category::create([
            'name' => 'Sports & Outdoors',
            'slug' => 'sports-outdoors',
            'description' => 'Sports equipment and outdoor gear',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        $books = Category::create([
            'name' => 'Books & Media',
            'slug' => 'books-media',
            'description' => 'Books, movies, music, and educational materials',
            'is_active' => true,
            'sort_order' => 5,
        ]);

        $beauty = Category::create([
            'name' => 'Beauty & Health',
            'slug' => 'beauty-health',
            'description' => 'Beauty products and health supplements',
            'is_active' => true,
            'sort_order' => 6,
        ]);

        // Electronics Subcategories
        $smartphones = Category::create([
            'name' => 'Smartphones',
            'slug' => 'smartphones',
            'parent_id' => $electronics->id,
            'description' => 'Latest smartphones and mobile devices',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $laptops = Category::create([
            'name' => 'Laptops & Computers',
            'slug' => 'laptops-computers',
            'parent_id' => $electronics->id,
            'description' => 'Laptops, desktops, and computer accessories',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $audio = Category::create([
            'name' => 'Audio & Headphones',
            'slug' => 'audio-headphones',
            'parent_id' => $electronics->id,
            'description' => 'Headphones, speakers, and audio equipment',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $cameras = Category::create([
            'name' => 'Cameras & Photography',
            'slug' => 'cameras-photography',
            'parent_id' => $electronics->id,
            'description' => 'Digital cameras, lenses, and photography gear',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        $gaming = Category::create([
            'name' => 'Gaming',
            'slug' => 'gaming',
            'parent_id' => $electronics->id,
            'description' => 'Gaming consoles, accessories, and games',
            'is_active' => true,
            'sort_order' => 5,
        ]);

        // Clothing Subcategories
        $mensClothing = Category::create([
            'name' => "Men's Clothing",
            'slug' => 'mens-clothing',
            'parent_id' => $clothing->id,
            'description' => 'Fashionable clothing for men',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $womensClothing = Category::create([
            'name' => "Women's Clothing",
            'slug' => 'womens-clothing',
            'parent_id' => $clothing->id,
            'description' => 'Fashionable clothing for women',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $kidsClothing = Category::create([
            'name' => "Kids' Clothing",
            'slug' => 'kids-clothing',
            'parent_id' => $clothing->id,
            'description' => 'Clothing for children and babies',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $shoes = Category::create([
            'name' => 'Shoes',
            'slug' => 'shoes',
            'parent_id' => $clothing->id,
            'description' => 'Footwear for all occasions',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        $accessories = Category::create([
            'name' => 'Fashion Accessories',
            'slug' => 'fashion-accessories',
            'parent_id' => $clothing->id,
            'description' => 'Bags, jewelry, watches, and other accessories',
            'is_active' => true,
            'sort_order' => 5,
        ]);

        // Home & Garden Subcategories
        $furniture = Category::create([
            'name' => 'Furniture',
            'slug' => 'furniture',
            'parent_id' => $homeGarden->id,
            'description' => 'Home and office furniture',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $kitchen = Category::create([
            'name' => 'Kitchen & Dining',
            'slug' => 'kitchen-dining',
            'parent_id' => $homeGarden->id,
            'description' => 'Kitchen appliances and dining accessories',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $decor = Category::create([
            'name' => 'Home Decor',
            'slug' => 'home-decor',
            'parent_id' => $homeGarden->id,
            'description' => 'Decorative items for your home',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $garden = Category::create([
            'name' => 'Garden & Outdoor',
            'slug' => 'garden-outdoor',
            'parent_id' => $homeGarden->id,
            'description' => 'Garden tools and outdoor equipment',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        // Sports Subcategories
        $fitness = Category::create([
            'name' => 'Fitness & Exercise',
            'slug' => 'fitness-exercise',
            'parent_id' => $sports->id,
            'description' => 'Fitness equipment and exercise gear',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $outdoor = Category::create([
            'name' => 'Outdoor Recreation',
            'slug' => 'outdoor-recreation',
            'parent_id' => $sports->id,
            'description' => 'Camping, hiking, and outdoor activities',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $teamSports = Category::create([
            'name' => 'Team Sports',
            'slug' => 'team-sports',
            'parent_id' => $sports->id,
            'description' => 'Equipment for team sports',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Books Subcategories
        $fiction = Category::create([
            'name' => 'Fiction Books',
            'slug' => 'fiction-books',
            'parent_id' => $books->id,
            'description' => 'Novels, short stories, and fiction literature',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $nonFiction = Category::create([
            'name' => 'Non-Fiction Books',
            'slug' => 'non-fiction-books',
            'parent_id' => $books->id,
            'description' => 'Educational and informational books',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $childrensBooks = Category::create([
            'name' => "Children's Books",
            'slug' => 'childrens-books',
            'parent_id' => $books->id,
            'description' => 'Books for children and young readers',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Beauty Subcategories
        $skincare = Category::create([
            'name' => 'Skincare',
            'slug' => 'skincare',
            'parent_id' => $beauty->id,
            'description' => 'Skincare products and treatments',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $makeup = Category::create([
            'name' => 'Makeup',
            'slug' => 'makeup',
            'parent_id' => $beauty->id,
            'description' => 'Cosmetics and makeup products',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $haircare = Category::create([
            'name' => 'Hair Care',
            'slug' => 'hair-care',
            'parent_id' => $beauty->id,
            'description' => 'Hair care products and treatments',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $fragrance = Category::create([
            'name' => 'Fragrance',
            'slug' => 'fragrance',
            'parent_id' => $beauty->id,
            'description' => 'Perfumes, colognes, and body sprays',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        // Third level categories for more specific products
        $mensShirts = Category::create([
            'name' => "Men's Shirts",
            'slug' => 'mens-shirts',
            'parent_id' => $mensClothing->id,
            'description' => 'Dress shirts, casual shirts, and polo shirts',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $mensPants = Category::create([
            'name' => "Men's Pants",
            'slug' => 'mens-pants',
            'parent_id' => $mensClothing->id,
            'description' => 'Jeans, dress pants, and casual pants',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $womensDresses = Category::create([
            'name' => "Women's Dresses",
            'slug' => 'womens-dresses',
            'parent_id' => $womensClothing->id,
            'description' => 'Casual dresses, formal dresses, and evening wear',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $womensTops = Category::create([
            'name' => "Women's Tops",
            'slug' => 'womens-tops',
            'parent_id' => $womensClothing->id,
            'description' => 'Blouses, t-shirts, and casual tops',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $mensShoes = Category::create([
            'name' => "Men's Shoes",
            'slug' => 'mens-shoes',
            'parent_id' => $shoes->id,
            'description' => 'Dress shoes, sneakers, and casual footwear',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $womensShoes = Category::create([
            'name' => "Women's Shoes",
            'slug' => 'womens-shoes',
            'parent_id' => $shoes->id,
            'description' => 'Heels, flats, sneakers, and boots',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $livingRoom = Category::create([
            'name' => 'Living Room Furniture',
            'slug' => 'living-room-furniture',
            'parent_id' => $furniture->id,
            'description' => 'Sofas, chairs, coffee tables, and entertainment centers',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $bedroom = Category::create([
            'name' => 'Bedroom Furniture',
            'slug' => 'bedroom-furniture',
            'parent_id' => $furniture->id,
            'description' => 'Beds, dressers, nightstands, and bedroom sets',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $kitchenAppliances = Category::create([
            'name' => 'Kitchen Appliances',
            'slug' => 'kitchen-appliances',
            'parent_id' => $kitchen->id,
            'description' => 'Refrigerators, ovens, microwaves, and small appliances',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $cookware = Category::create([
            'name' => 'Cookware & Bakeware',
            'slug' => 'cookware-bakeware',
            'parent_id' => $kitchen->id,
            'description' => 'Pots, pans, baking dishes, and cooking utensils',
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }
}
