<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ProductDetailSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create attributes
        $colorAttr = Attribute::firstOrCreate(
            ['name' => 'Color'],
            [
                'slug' => 'color',
                'type' => 'select',
                'is_required' => true,
                'is_filterable' => true,
                'is_visible_on_frontend' => true
            ]
        );

        $storageAttr = Attribute::firstOrCreate(
            ['name' => 'Storage'],
            [
                'slug' => 'storage',
                'type' => 'select',
                'is_required' => true,
                'is_filterable' => true,
                'is_visible_on_frontend' => true
            ]
        );

        $sizeAttr = Attribute::firstOrCreate(
            ['name' => 'Size'],
            [
                'slug' => 'size',
                'type' => 'select',
                'is_required' => true,
                'is_filterable' => true,
                'is_visible_on_frontend' => true
            ]
        );

        // Get products for detail creation
        $products = Product::all();

        foreach ($products as $product) {
            $this->createProductDetails($product, [
                'color' => $colorAttr->id,
                'storage' => $storageAttr->id,
                'size' => $sizeAttr->id
            ]);
        }
    }

    private function createProductDetails(Product $product, array $attributeIds): void
    {
        $productSlug = $product->slug;
        $productName = $product->name;

        // Common product details based on category
        $category = $this->getProductCategory($productSlug);
        
        switch ($category) {
            case 'smartphone':
                $this->seedSmartphoneVariants($product, $attributeIds);
                break;
                
            case 'laptop':
                $this->seedLaptopVariants($product, $attributeIds);
                break;
                
            case 'clothing':
                $this->seedClothingVariants($product, $attributeIds);
                break;
                
            default:
                $this->seedGenericProduct($product, $attributeIds);
        }
    }
    
    private function getProductCategory(string $slug): string
    {
        if (str_contains($slug, 'iphone') || str_contains($slug, 'galaxy') || str_contains($slug, 'pixel')) {
            return 'smartphone';
        }
        
        if (str_contains($slug, 'macbook') || str_contains($slug, 'surface') || str_contains($slug, 'xps')) {
            return 'laptop';
        }
        
        if (str_contains($slug, 't-shirt') || str_contains($slug, 'jeans') || str_contains($slug, 'dress')) {
            return 'clothing';
        }
        
        return 'other';
    }
    
    private function seedSmartphoneVariants(Product $product, array $attributeIds): void
    {
        $colors = [
            'Black', 'White', 'Blue', 'Red', 'Green', 'Purple', 'Gold', 'Silver'
        ];
        
        $storages = ['64GB', '128GB', '256GB', '512GB', '1TB'];
        
        // Generate 3-5 random variants per product
        $variantCount = rand(3, 5);
        $createdVariants = [];
        
        for ($i = 0; $i < $variantCount; $i++) {
            $color = $colors[array_rand($colors)];
            $storage = $storages[array_rand($storages)];
            $variantKey = "{$color}_{$storage}";
            
            // Ensure unique variants
            if (in_array($variantKey, $createdVariants)) {
                $i--;
                continue;
            }
            
            $createdVariants[] = $variantKey;
            
            // Calculate price based on storage
            $basePrice = $this->getBasePrice($product->name);
            $price = $basePrice + (array_search($storage, $storages) * 100);
            
            // Create product detail
            $detail = ProductDetail::create([
                'product_id' => $product->id,
                'color' => $color,
                'price' => $price,
                'discount' => rand(0, 1) ? rand(5, 20) : 0, // 50% chance of having a discount
                'stock' => rand(5, 100),
                'min_stock_alert' => 5,
                'sku_variant' => 'V' . strtoupper(Str::random(4)) . '-' . $i,
                'is_active' => true,
            ]);
            
            // Attach attributes
            $this->attachAttributeValue($detail->id, $attributeIds['color'], $color);
            $this->attachAttributeValue($detail->id, $attributeIds['storage'], $storage);
        }
    }
    
    private function seedLaptopVariants(Product $product, array $attributeIds): void
    {
        $colors = ['Silver', 'Space Gray', 'Black', 'Blue', 'White'];
        $storages = ['256GB SSD', '512GB SSD', '1TB SSD', '2TB SSD'];
        $rams = ['8GB', '16GB', '32GB', '64GB'];
        
        $variantCount = rand(2, 4);
        $createdVariants = [];
        
        for ($i = 0; $i < $variantCount; $i++) {
            $color = $colors[array_rand($colors)];
            $storage = $storages[array_rand($storages)];
            $ram = $rams[array_rand($rams)];
            
            $variantKey = "{$color}_{$storage}_{$ram}";
            
            if (in_array($variantKey, $createdVariants)) {
                $i--;
                continue;
            }
            
            $createdVariants[] = $variantKey;
            
            $basePrice = $this->getBasePrice($product->name);
            $price = $basePrice + (array_search($storage, $storages) * 200) + (array_search($ram, $rams) * 100);
            
            $detail = ProductDetail::create([
                'product_id' => $product->id,
                'color' => $color,
                'price' => $price,
                'discount' => rand(0, 1) ? rand(5, 15) : 0,
                'stock' => rand(3, 50),
                'min_stock_alert' => 2,
                'sku_variant' => 'L' . strtoupper(Str::random(4)) . '-' . $i,
                'is_active' => true,
            ]);
            
            $this->attachAttributeValue($detail->id, $attributeIds['color'], $color);
            $this->attachAttributeValue($detail->id, $attributeIds['storage'], $storage);
        }
    }
    
    private function seedClothingVariants(Product $product, array $attributeIds): void
    {
        $colors = ['Black', 'White', 'Blue', 'Red', 'Green', 'Gray'];
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        
        $variantCount = rand(3, 6);
        $createdVariants = [];
        
        for ($i = 0; $i < $variantCount; $i++) {
            $color = $colors[array_rand($colors)];
            $size = $sizes[array_rand($sizes)];
            
            $variantKey = "{$color}_{$size}";
            
            if (in_array($variantKey, $createdVariants)) {
                $i--;
                continue;
            }
            
            $createdVariants[] = $variantKey;
            
            $basePrice = $this->getBasePrice($product->name);
            $price = $basePrice + (array_search($size, $sizes) * 5);
            
            $detail = ProductDetail::create([
                'product_id' => $product->id,
                'color' => $color,
                'price' => $price,
                'discount' => rand(0, 1) ? rand(5, 30) : 0,
                'stock' => rand(10, 200),
                'min_stock_alert' => 10,
                'sku_variant' => 'C' . strtoupper(Str::random(4)) . '-' . $i,
                'is_active' => true,
            ]);
            
            $this->attachAttributeValue($detail->id, $attributeIds['color'], $color);
            $this->attachAttributeValue($detail->id, $attributeIds['size'], $size);
        }
    }
    
    private function seedGenericProduct(Product $product, array $attributeIds): void
    {
        // For products that don't fit other categories
        $detail = ProductDetail::create([
            'product_id' => $product->id,
            'price' => $this->getBasePrice($product->name),
            'discount' => rand(0, 1) ? rand(5, 25) : 0,
            'stock' => rand(1, 50),
            'min_stock_alert' => 5,
            'sku_variant' => 'P' . strtoupper(Str::random(4)),
            'is_active' => true,
        ]);
    }
    
    private function getBasePrice(string $productName): float
    {
        // Simple price calculation based on product name
        $basePrice = 99.99; // Default base price
        
        if (str_contains(strtolower($productName), 'pro') || 
            str_contains(strtolower($productName), 'ultra')) {
            $basePrice = 999.99;
        } elseif (str_contains(strtolower($productName), 'air') || 
                 str_contains(strtolower($productName), 'lite')) {
            $basePrice = 499.99;
        }
        
        // Add some random variation
        return $basePrice + rand(0, 200);
    }
    
    private function attachAttributeValue(int $productDetailId, int $attributeId, string $value): void
    {
        try {
            AttributeValue::create([
                'product_detail_id' => $productDetailId,
                'attribute_id' => $attributeId,
                'value' => $value,
                'value_type' => 'string',
            ]);
        } catch (\Exception $e) {
            // Log error but don't stop execution
            \Log::error("Failed to attach attribute value: " . $e->getMessage());
        }
    }

    private function createSmartphoneDetails(Product $product, string $name, array $variants): void
    {
        foreach ($variants as $variant) {
            ProductDetail::create([
                'product_id' => $product->id,
                'color' => $variant['color'],
                'size' => $variant['storage'],
                'price' => $variant['price'],
                'stock' => rand(10, 100),
                'min_stock_alert' => 5,
                'sku_variant' => $product->sku . '-' . str_replace(' ', '', $variant['color']) . '-' . $variant['storage'],
                'barcode' => '8' . rand(100000000000, 999999999999),
                'is_active' => true,
                'material' => 'Glass, Titanium',
                'weight' => 0.187,
                'origin' => 'China',
                'quality' => 'Premium',
                'packaging' => 'Original Box',
            ]);
        }
    }

    private function createLaptopDetails(Product $product, string $name, array $variants): void
    {
        foreach ($variants as $variant) {
            ProductDetail::create([
                'product_id' => $product->id,
                'size' => $variant['config'],
                'price' => $variant['price'],
                'stock' => rand(5, 50),
                'min_stock_alert' => 3,
                'sku_variant' => $product->sku . '-' . substr(md5($variant['config']), 0, 8),
                'barcode' => '8' . rand(100000000000, 999999999999),
                'is_active' => true,
                'material' => 'Aluminum, Plastic',
                'weight' => rand(15, 25) / 10,
                'origin' => 'China',
                'quality' => 'Professional',
                'packaging' => 'Original Box',
            ]);
        }
    }

    private function createAudioDetails(Product $product, string $name, array $variants): void
    {
        foreach ($variants as $variant) {
            ProductDetail::create([
                'product_id' => $product->id,
                'color' => $variant['color'],
                'price' => $variant['price'],
                'stock' => rand(20, 100),
                'min_stock_alert' => 10,
                'sku_variant' => $product->sku . '-' . str_replace(' ', '', $variant['color']),
                'barcode' => '8' . rand(100000000000, 999999999999),
                'is_active' => true,
                'material' => 'Plastic, Metal',
                'weight' => rand(5, 15) / 10,
                'origin' => 'China',
                'quality' => 'Premium',
                'packaging' => 'Original Box',
            ]);
        }
    }

    private function createClothingDetails(Product $product, string $name, array $variants): void
    {
        foreach ($variants as $variant) {
            $color = $variant['color'] ?? 'Standard';
            ProductDetail::create([
                'product_id' => $product->id,
                'size' => $variant['size'],
                'color' => $color,
                'price' => $variant['price'],
                'stock' => rand(5, 50),
                'min_stock_alert' => 3,
                'sku_variant' => $product->sku . '-' . $variant['size'] . '-' . str_replace(' ', '', $color),
                'barcode' => '8' . rand(100000000000, 999999999999),
                'is_active' => true,
                'material' => 'Cotton, Polyester',
                'weight' => rand(1, 5) / 10,
                'origin' => 'Bangladesh',
                'quality' => 'Standard',
                'packaging' => 'Plastic Bag',
            ]);
        }
    }

    private function createShoeDetails(Product $product, string $name, array $variants): void
    {
        foreach ($variants as $variant) {
            ProductDetail::create([
                'product_id' => $product->id,
                'size' => $variant['size'],
                'price' => $variant['price'],
                'stock' => rand(5, 30),
                'min_stock_alert' => 3,
                'sku_variant' => $product->sku . '-' . $variant['size'],
                'barcode' => '8' . rand(100000000000, 999999999999),
                'is_active' => true,
                'material' => 'Leather, Rubber',
                'weight' => rand(5, 15) / 10,
                'origin' => 'Italy',
                'quality' => 'Premium',
                'packaging' => 'Shoe Box',
            ]);
        }
    }

    private function createFurnitureDetails(Product $product, string $name, array $variants): void
    {
        foreach ($variants as $variant) {
            $size = $variant['size'] ?? '';
            ProductDetail::create([
                'product_id' => $product->id,
                'size' => $size,
                'color' => $variant['variant'],
                'price' => $variant['price'],
                'stock' => rand(2, 20),
                'min_stock_alert' => 2,
                'sku_variant' => $product->sku . '-' . str_replace(' ', '', $variant['variant']) . ($size ? '-' . $size : ''),
                'barcode' => '8' . rand(100000000000, 999999999999),
                'is_active' => true,
                'material' => 'Wood, Fabric',
                'weight' => rand(20, 100) / 10,
                'origin' => 'USA',
                'quality' => 'Premium',
                'packaging' => 'Flat Pack',
            ]);
        }
    }

    private function createKitchenDetails(Product $product, string $name, array $variants): void
    {
        foreach ($variants as $variant) {
            ProductDetail::create([
                'product_id' => $product->id,
                'size' => $variant['variant'],
                'price' => $variant['price'],
                'stock' => rand(3, 25),
                'min_stock_alert' => 2,
                'sku_variant' => $product->sku . '-' . str_replace(' ', '', $variant['variant']),
                'barcode' => '8' . rand(100000000000, 999999999999),
                'is_active' => true,
                'material' => 'Stainless Steel',
                'weight' => rand(10, 50) / 10,
                'origin' => 'USA',
                'quality' => 'Professional',
                'packaging' => 'Original Box',
            ]);
        }
    }

    private function createSportsDetails(Product $product, string $name, array $variants): void
    {
        foreach ($variants as $variant) {
            ProductDetail::create([
                'product_id' => $product->id,
                'size' => $variant['variant'],
                'price' => $variant['price'],
                'stock' => rand(5, 30),
                'min_stock_alert' => 3,
                'sku_variant' => $product->sku . '-' . str_replace(' ', '', $variant['variant']),
                'barcode' => '8' . rand(100000000000, 999999999999),
                'is_active' => true,
                'material' => 'Steel, Plastic',
                'weight' => rand(10, 50) / 10,
                'origin' => 'China',
                'quality' => 'Standard',
                'packaging' => 'Original Box',
            ]);
        }
    }

    private function createBookDetails(Product $product, string $name, array $variants): void
    {
        foreach ($variants as $variant) {
            ProductDetail::create([
                'product_id' => $product->id,
                'size' => $variant['variant'],
                'price' => $variant['price'],
                'stock' => rand(10, 100),
                'min_stock_alert' => 5,
                'sku_variant' => $product->sku . '-' . str_replace(' ', '', $variant['variant']),
                'barcode' => '9' . rand(100000000000, 999999999999),
                'is_active' => true,
                'material' => 'Paper',
                'weight' => rand(2, 8) / 10,
                'origin' => 'USA',
                'quality' => 'Standard',
                'packaging' => 'None',
            ]);
        }
    }

    private function createBeautyDetails(Product $product, string $name, array $variants): void
    {
        foreach ($variants as $variant) {
            ProductDetail::create([
                'product_id' => $product->id,
                'size' => $variant['variant'],
                'price' => $variant['price'],
                'stock' => rand(15, 80),
                'min_stock_alert' => 5,
                'sku_variant' => $product->sku . '-' . str_replace(' ', '', $variant['variant']),
                'barcode' => '8' . rand(100000000000, 999999999999),
                'is_active' => true,
                'material' => 'Glass, Plastic',
                'weight' => rand(1, 5) / 10,
                'origin' => 'USA',
                'quality' => 'Premium',
                'packaging' => 'Original Box',
            ]);
        }
    }

    private function createDefaultDetails(Product $product): void
    {
        ProductDetail::create([
            'product_id' => $product->id,
            'price' => rand(50, 500),
            'stock' => rand(5, 50),
            'min_stock_alert' => 3,
            'sku_variant' => $product->sku . '-STD',
            'barcode' => '8' . rand(100000000000, 999999999999),
            'is_active' => true,
            'material' => 'Standard',
            'weight' => rand(1, 10) / 10,
            'origin' => 'China',
            'quality' => 'Standard',
            'packaging' => 'Original Box',
        ]);
    }
}
