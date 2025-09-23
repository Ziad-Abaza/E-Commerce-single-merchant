<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories for product assignment
        $smartphones = Category::where('slug', 'smartphones')->first();
        $laptops = Category::where('slug', 'laptops-computers')->first();
        $audio = Category::where('slug', 'audio-headphones')->first();
        $cameras = Category::where('slug', 'cameras-photography')->first();
        $gaming = Category::where('slug', 'gaming')->first();
        $mensShirts = Category::where('slug', 'mens-shirts')->first();
        $mensPants = Category::where('slug', 'mens-pants')->first();
        $womensDresses = Category::where('slug', 'womens-dresses')->first();
        $womensTops = Category::where('slug', 'womens-tops')->first();
        $mensShoes = Category::where('slug', 'mens-shoes')->first();
        $womensShoes = Category::where('slug', 'womens-shoes')->first();
        $livingRoom = Category::where('slug', 'living-room-furniture')->first();
        $bedroom = Category::where('slug', 'bedroom-furniture')->first();
        $kitchenAppliances = Category::where('slug', 'kitchen-appliances')->first();
        $cookware = Category::where('slug', 'cookware-bakeware')->first();
        $fitness = Category::where('slug', 'fitness-exercise')->first();
        $outdoor = Category::where('slug', 'outdoor-recreation')->first();
        $fiction = Category::where('slug', 'fiction-books')->first();
        $skincare = Category::where('slug', 'skincare')->first();
        $makeup = Category::where('slug', 'makeup')->first();

        // Electronics Products
        $products = [
            // Smartphones
            [
                'name' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'brand' => 'Apple',
                'short_description' => 'Latest iPhone with advanced camera system and titanium design',
                'description' => 'The iPhone 15 Pro features a titanium design, advanced camera system with 5x telephoto zoom, A17 Pro chip, and USB-C connectivity. Perfect for photography enthusiasts and power users.',
                'sku' => 'IPH15PRO-128',
                'is_active' => true,
                'categories' => [$smartphones->id],
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'slug' => 'samsung-galaxy-s24-ultra',
                'brand' => 'Samsung',
                'short_description' => 'Premium Android smartphone with S Pen and advanced AI features',
                'description' => 'The Galaxy S24 Ultra combines powerful performance with innovative features like the S Pen, advanced camera system, and AI-powered capabilities for the ultimate mobile experience.',
                'sku' => 'SGS24U-256',
                'is_active' => true,
                'categories' => [$smartphones->id],
            ],
            [
                'name' => 'Google Pixel 8 Pro',
                'slug' => 'google-pixel-8-pro',
                'brand' => 'Google',
                'short_description' => 'AI-powered smartphone with exceptional camera capabilities',
                'description' => 'The Pixel 8 Pro delivers cutting-edge AI features, exceptional photography with Google\'s computational photography, and seamless integration with Google services.',
                'sku' => 'GP8P-128',
                'is_active' => true,
                'categories' => [$smartphones->id],
            ],

            // Laptops
            [
                'name' => 'MacBook Pro 16-inch M3 Max',
                'slug' => 'macbook-pro-16-m3-max',
                'brand' => 'Apple',
                'short_description' => 'Professional laptop with M3 Max chip for demanding tasks',
                'description' => 'The MacBook Pro 16-inch with M3 Max chip delivers exceptional performance for professional work, creative projects, and intensive computing tasks.',
                'sku' => 'MBP16-M3MAX-1TB',
                'is_active' => true,
                'categories' => [$laptops->id],
            ],
            [
                'name' => 'Dell XPS 15',
                'slug' => 'dell-xps-15',
                'brand' => 'Dell',
                'short_description' => 'Premium Windows laptop with stunning 4K display',
                'description' => 'The Dell XPS 15 combines powerful Intel processors with a beautiful 4K InfinityEdge display, perfect for content creators and professionals.',
                'sku' => 'DXPS15-512',
                'is_active' => true,
                'categories' => [$laptops->id],
            ],
            [
                'name' => 'ASUS ROG Strix G15',
                'slug' => 'asus-rog-strix-g15',
                'brand' => 'ASUS',
                'short_description' => 'Gaming laptop with high-performance graphics and fast refresh rate',
                'description' => 'The ROG Strix G15 delivers exceptional gaming performance with high-end graphics cards, fast processors, and high refresh rate displays.',
                'sku' => 'AROG-G15-1TB',
                'is_active' => true,
                'categories' => [$laptops->id],
            ],

            // Audio
            [
                'name' => 'Sony WH-1000XM5',
                'slug' => 'sony-wh-1000xm5',
                'brand' => 'Sony',
                'short_description' => 'Premium noise-canceling wireless headphones',
                'description' => 'Industry-leading noise cancellation, exceptional sound quality, and comfortable design make these headphones perfect for music lovers and frequent travelers.',
                'sku' => 'SWH1000XM5',
                'is_active' => true,
                'categories' => [$audio->id],
            ],
            [
                'name' => 'AirPods Pro (2nd Generation)',
                'slug' => 'airpods-pro-2nd-gen',
                'brand' => 'Apple',
                'short_description' => 'Wireless earbuds with active noise cancellation',
                'description' => 'The AirPods Pro feature active noise cancellation, spatial audio, and seamless integration with Apple devices for the ultimate wireless audio experience.',
                'sku' => 'APP2-GEN2',
                'is_active' => true,
                'categories' => [$audio->id],
            ],

            // Cameras
            [
                'name' => 'Canon EOS R5',
                'slug' => 'canon-eos-r5',
                'brand' => 'Canon',
                'short_description' => 'Professional mirrorless camera with 8K video recording',
                'description' => 'The EOS R5 delivers professional-grade photography and videography with 8K video recording, high-resolution stills, and advanced autofocus.',
                'sku' => 'CER5-BODY',
                'is_active' => true,
                'categories' => [$cameras->id],
            ],
            [
                'name' => 'Sony A7 IV',
                'slug' => 'sony-a7-iv',
                'brand' => 'Sony',
                'short_description' => 'Full-frame mirrorless camera with 4K video',
                'description' => 'The A7 IV combines excellent image quality with advanced video capabilities, making it perfect for both photographers and videographers.',
                'sku' => 'SA7IV-BODY',
                'is_active' => true,
                'categories' => [$cameras->id],
            ],

            // Gaming
            [
                'name' => 'PlayStation 5',
                'slug' => 'playstation-5',
                'brand' => 'Sony',
                'short_description' => 'Next-generation gaming console with 4K gaming',
                'description' => 'Experience the future of gaming with the PlayStation 5, featuring ultra-fast SSD, 4K gaming, and immersive 3D audio.',
                'sku' => 'PS5-STD',
                'is_active' => true,
                'categories' => [$gaming->id],
            ],
            [
                'name' => 'Xbox Series X',
                'slug' => 'xbox-series-x',
                'brand' => 'Microsoft',
                'short_description' => 'Most powerful Xbox console with 4K gaming',
                'description' => 'The Xbox Series X delivers true 4K gaming, faster load times, and backward compatibility with thousands of games.',
                'sku' => 'XSX-STD',
                'is_active' => true,
                'categories' => [$gaming->id],
            ],

            // Clothing - Men's
            [
                'name' => 'Classic White Dress Shirt',
                'slug' => 'classic-white-dress-shirt',
                'brand' => 'Tommy Hilfiger',
                'short_description' => 'Premium cotton dress shirt for formal occasions',
                'description' => 'Made from premium cotton, this classic white dress shirt features a traditional fit and is perfect for business meetings and formal events.',
                'sku' => 'TH-DS-WHITE-M',
                'is_active' => true,
                'categories' => [$mensShirts->id],
            ],
            [
                'name' => 'Slim Fit Jeans',
                'slug' => 'slim-fit-jeans',
                'brand' => 'Levi\'s',
                'short_description' => 'Comfortable slim fit jeans in classic blue',
                'description' => 'Classic blue denim jeans with a modern slim fit. Made from durable cotton denim with stretch for comfort and style.',
                'sku' => 'LV-SFJ-BLUE-32',
                'is_active' => true,
                'categories' => [$mensPants->id],
            ],

            // Clothing - Women's
            [
                'name' => 'Elegant Black Dress',
                'slug' => 'elegant-black-dress',
                'brand' => 'Calvin Klein',
                'short_description' => 'Sophisticated black dress for evening events',
                'description' => 'A timeless black dress with elegant design, perfect for evening events, parties, and special occasions.',
                'sku' => 'CK-ED-BLK-S',
                'is_active' => true,
                'categories' => [$womensDresses->id],
            ],
            [
                'name' => 'Casual Cotton T-Shirt',
                'slug' => 'casual-cotton-t-shirt',
                'brand' => 'Uniqlo',
                'short_description' => 'Soft cotton t-shirt in various colors',
                'description' => 'Comfortable and versatile cotton t-shirt, perfect for everyday wear. Available in multiple colors and sizes.',
                'sku' => 'UQ-CT-BLK-M',
                'is_active' => true,
                'categories' => [$womensTops->id],
            ],

            // Shoes
            [
                'name' => 'Leather Oxford Shoes',
                'slug' => 'leather-oxford-shoes',
                'brand' => 'Cole Haan',
                'short_description' => 'Classic leather oxford shoes for formal wear',
                'description' => 'Handcrafted leather oxford shoes with traditional styling and modern comfort technology.',
                'sku' => 'CH-OX-BLK-10',
                'is_active' => true,
                'categories' => [$mensShoes->id],
            ],
            [
                'name' => 'High Heel Pump',
                'slug' => 'high-heel-pump',
                'brand' => 'Nine West',
                'short_description' => 'Classic high heel pump in black leather',
                'description' => 'Timeless black leather pump with comfortable heel height, perfect for office and evening wear.',
                'sku' => 'NW-HP-BLK-8',
                'is_active' => true,
                'categories' => [$womensShoes->id],
            ],

            // Furniture
            [
                'name' => 'Modern Sectional Sofa',
                'slug' => 'modern-sectional-sofa',
                'brand' => 'West Elm',
                'short_description' => 'Contemporary sectional sofa with clean lines',
                'description' => 'Modern sectional sofa featuring clean lines, premium upholstery, and modular design for flexible living room arrangements.',
                'sku' => 'WE-SS-GRY',
                'is_active' => true,
                'categories' => [$livingRoom->id],
            ],
            [
                'name' => 'Platform Bed Frame',
                'slug' => 'platform-bed-frame',
                'brand' => 'IKEA',
                'short_description' => 'Minimalist platform bed frame in oak',
                'description' => 'Clean and minimalist platform bed frame made from solid oak wood, perfect for modern bedroom decor.',
                'sku' => 'IK-PBF-OAK-Q',
                'is_active' => true,
                'categories' => [$bedroom->id],
            ],

            // Kitchen
            [
                'name' => 'Smart Refrigerator',
                'slug' => 'smart-refrigerator',
                'brand' => 'Samsung',
                'short_description' => 'Large capacity smart refrigerator with touchscreen',
                'description' => 'Smart refrigerator with large capacity, touchscreen display, and Wi-Fi connectivity for modern kitchen convenience.',
                'sku' => 'SS-SRF-28CF',
                'is_active' => true,
                'categories' => [$kitchenAppliances->id],
            ],
            [
                'name' => 'Stainless Steel Cookware Set',
                'slug' => 'stainless-steel-cookware-set',
                'brand' => 'All-Clad',
                'short_description' => 'Professional-grade stainless steel cookware set',
                'description' => 'Professional-grade stainless steel cookware set with tri-ply construction for even heat distribution and durability.',
                'sku' => 'AC-SCS-10PC',
                'is_active' => true,
                'categories' => [$cookware->id],
            ],

            // Sports
            [
                'name' => 'Adjustable Dumbbells',
                'slug' => 'adjustable-dumbbells',
                'brand' => 'Bowflex',
                'short_description' => 'Space-saving adjustable dumbbells for home gym',
                'description' => 'Space-saving adjustable dumbbells that replace multiple sets of weights, perfect for home fitness routines.',
                'sku' => 'BF-AD-552',
                'is_active' => true,
                'categories' => [$fitness->id],
            ],
            [
                'name' => 'Camping Tent',
                'slug' => 'camping-tent',
                'brand' => 'Coleman',
                'short_description' => '4-person dome tent for camping adventures',
                'description' => 'Durable 4-person dome tent with easy setup, weather protection, and spacious interior for comfortable camping.',
                'sku' => 'CM-CT-4P',
                'is_active' => true,
                'categories' => [$outdoor->id],
            ],

            // Books
            [
                'name' => 'The Great Gatsby',
                'slug' => 'the-great-gatsby',
                'brand' => 'F. Scott Fitzgerald',
                'short_description' => 'Classic American novel about the Jazz Age',
                'description' => 'A timeless classic that captures the essence of the Jazz Age and explores themes of wealth, love, and the American Dream.',
                'sku' => 'TGG-PB-EN',
                'is_active' => true,
                'categories' => [$fiction->id],
            ],

            // Beauty
            [
                'name' => 'Anti-Aging Serum',
                'slug' => 'anti-aging-serum',
                'brand' => 'Olay',
                'short_description' => 'Advanced anti-aging serum with retinol',
                'description' => 'Advanced anti-aging serum formulated with retinol and other powerful ingredients to reduce fine lines and improve skin texture.',
                'sku' => 'OL-AAS-30ML',
                'is_active' => true,
                'categories' => [$skincare->id],
            ],
            [
                'name' => 'Matte Lipstick Set',
                'slug' => 'matte-lipstick-set',
                'brand' => 'MAC',
                'short_description' => 'Set of 6 matte lipsticks in popular shades',
                'description' => 'Professional-quality matte lipstick set featuring 6 popular shades, perfect for creating various looks.',
                'sku' => 'MAC-MLS-6PC',
                'is_active' => true,
                'categories' => [$makeup->id],
            ],
        ];

        foreach ($products as $productData) {
            $categories = $productData['categories'];
            unset($productData['categories']);
            
            $product = Product::create($productData);
            $product->categories()->attach($categories);
        }
    }
}
