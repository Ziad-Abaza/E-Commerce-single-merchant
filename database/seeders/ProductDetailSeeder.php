<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Database\Seeder;

class ProductDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get products for detail creation
        $products = Product::all();

        foreach ($products as $product) {
            $this->createProductDetails($product);
        }
    }

    private function createProductDetails(Product $product): void
    {
        $productSlug = $product->slug;

        switch ($productSlug) {
            // Smartphones - Multiple colors and storage options
            case 'iphone-15-pro':
                $this->createSmartphoneDetails($product, 'iPhone 15 Pro', [
                    ['color' => 'Natural Titanium', 'storage' => '128GB', 'price' => 999.00],
                    ['color' => 'Natural Titanium', 'storage' => '256GB', 'price' => 1099.00],
                    ['color' => 'Natural Titanium', 'storage' => '512GB', 'price' => 1299.00],
                    ['color' => 'Blue Titanium', 'storage' => '128GB', 'price' => 999.00],
                    ['color' => 'Blue Titanium', 'storage' => '256GB', 'price' => 1099.00],
                    ['color' => 'Blue Titanium', 'storage' => '512GB', 'price' => 1299.00],
                    ['color' => 'White Titanium', 'storage' => '128GB', 'price' => 999.00],
                    ['color' => 'White Titanium', 'storage' => '256GB', 'price' => 1099.00],
                    ['color' => 'White Titanium', 'storage' => '512GB', 'price' => 1299.00],
                ]);
                break;

            case 'samsung-galaxy-s24-ultra':
                $this->createSmartphoneDetails($product, 'Galaxy S24 Ultra', [
                    ['color' => 'Titanium Black', 'storage' => '256GB', 'price' => 1199.00],
                    ['color' => 'Titanium Black', 'storage' => '512GB', 'price' => 1319.00],
                    ['color' => 'Titanium Black', 'storage' => '1TB', 'price' => 1559.00],
                    ['color' => 'Titanium Gray', 'storage' => '256GB', 'price' => 1199.00],
                    ['color' => 'Titanium Gray', 'storage' => '512GB', 'price' => 1319.00],
                    ['color' => 'Titanium Gray', 'storage' => '1TB', 'price' => 1559.00],
                    ['color' => 'Titanium Violet', 'storage' => '256GB', 'price' => 1199.00],
                    ['color' => 'Titanium Violet', 'storage' => '512GB', 'price' => 1319.00],
                ]);
                break;

            case 'google-pixel-8-pro':
                $this->createSmartphoneDetails($product, 'Pixel 8 Pro', [
                    ['color' => 'Obsidian', 'storage' => '128GB', 'price' => 999.00],
                    ['color' => 'Obsidian', 'storage' => '256GB', 'price' => 1059.00],
                    ['color' => 'Obsidian', 'storage' => '512GB', 'price' => 1179.00],
                    ['color' => 'Porcelain', 'storage' => '128GB', 'price' => 999.00],
                    ['color' => 'Porcelain', 'storage' => '256GB', 'price' => 1059.00],
                    ['color' => 'Porcelain', 'storage' => '512GB', 'price' => 1179.00],
                    ['color' => 'Bay', 'storage' => '128GB', 'price' => 999.00],
                    ['color' => 'Bay', 'storage' => '256GB', 'price' => 1059.00],
                ]);
                break;

            // Laptops - Different configurations
            case 'macbook-pro-16-m3-max':
                $this->createLaptopDetails($product, 'MacBook Pro 16"', [
                    ['config' => 'M3 Max, 32GB RAM, 1TB SSD', 'price' => 3499.00],
                    ['config' => 'M3 Max, 64GB RAM, 1TB SSD', 'price' => 4099.00],
                    ['config' => 'M3 Max, 32GB RAM, 2TB SSD', 'price' => 3899.00],
                    ['config' => 'M3 Max, 64GB RAM, 2TB SSD', 'price' => 4499.00],
                ]);
                break;

            case 'dell-xps-15':
                $this->createLaptopDetails($product, 'Dell XPS 15', [
                    ['config' => 'i7, 16GB RAM, 512GB SSD', 'price' => 1899.00],
                    ['config' => 'i7, 32GB RAM, 512GB SSD', 'price' => 2199.00],
                    ['config' => 'i7, 16GB RAM, 1TB SSD', 'price' => 2099.00],
                    ['config' => 'i7, 32GB RAM, 1TB SSD', 'price' => 2399.00],
                ]);
                break;

            // Audio - Different colors
            case 'sony-wh-1000xm5':
                $this->createAudioDetails($product, 'Sony WH-1000XM5', [
                    ['color' => 'Black', 'price' => 399.99],
                    ['color' => 'Silver', 'price' => 399.99],
                    ['color' => 'Midnight Blue', 'price' => 399.99],
                ]);
                break;

            case 'airpods-pro-2nd-gen':
                $this->createAudioDetails($product, 'AirPods Pro (2nd Gen)', [
                    ['color' => 'White', 'price' => 249.00],
                ]);
                break;

            // Clothing - Different sizes
            case 'classic-white-dress-shirt':
                $this->createClothingDetails($product, 'Classic White Dress Shirt', [
                    ['size' => 'S', 'price' => 89.99],
                    ['size' => 'M', 'price' => 89.99],
                    ['size' => 'L', 'price' => 89.99],
                    ['size' => 'XL', 'price' => 89.99],
                    ['size' => 'XXL', 'price' => 89.99],
                ]);
                break;

            case 'slim-fit-jeans':
                $this->createClothingDetails($product, 'Slim Fit Jeans', [
                    ['size' => '28x30', 'price' => 79.99],
                    ['size' => '30x30', 'price' => 79.99],
                    ['size' => '32x30', 'price' => 79.99],
                    ['size' => '34x30', 'price' => 79.99],
                    ['size' => '36x30', 'price' => 79.99],
                    ['size' => '28x32', 'price' => 79.99],
                    ['size' => '30x32', 'price' => 79.99],
                    ['size' => '32x32', 'price' => 79.99],
                    ['size' => '34x32', 'price' => 79.99],
                    ['size' => '36x32', 'price' => 79.99],
                ]);
                break;

            case 'elegant-black-dress':
                $this->createClothingDetails($product, 'Elegant Black Dress', [
                    ['size' => 'XS', 'price' => 129.99],
                    ['size' => 'S', 'price' => 129.99],
                    ['size' => 'M', 'price' => 129.99],
                    ['size' => 'L', 'price' => 129.99],
                    ['size' => 'XL', 'price' => 129.99],
                ]);
                break;

            case 'casual-cotton-t-shirt':
                $this->createClothingDetails($product, 'Casual Cotton T-Shirt', [
                    ['size' => 'XS', 'color' => 'Black', 'price' => 19.99],
                    ['size' => 'S', 'color' => 'Black', 'price' => 19.99],
                    ['size' => 'M', 'color' => 'Black', 'price' => 19.99],
                    ['size' => 'L', 'color' => 'Black', 'price' => 19.99],
                    ['size' => 'XL', 'color' => 'Black', 'price' => 19.99],
                    ['size' => 'XS', 'color' => 'White', 'price' => 19.99],
                    ['size' => 'S', 'color' => 'White', 'price' => 19.99],
                    ['size' => 'M', 'color' => 'White', 'price' => 19.99],
                    ['size' => 'L', 'color' => 'White', 'price' => 19.99],
                    ['size' => 'XL', 'color' => 'White', 'price' => 19.99],
                    ['size' => 'XS', 'color' => 'Navy', 'price' => 19.99],
                    ['size' => 'S', 'color' => 'Navy', 'price' => 19.99],
                    ['size' => 'M', 'color' => 'Navy', 'price' => 19.99],
                    ['size' => 'L', 'color' => 'Navy', 'price' => 19.99],
                    ['size' => 'XL', 'color' => 'Navy', 'price' => 19.99],
                ]);
                break;

            // Shoes - Different sizes
            case 'leather-oxford-shoes':
                $this->createShoeDetails($product, 'Leather Oxford Shoes', [
                    ['size' => '8', 'price' => 199.99],
                    ['size' => '8.5', 'price' => 199.99],
                    ['size' => '9', 'price' => 199.99],
                    ['size' => '9.5', 'price' => 199.99],
                    ['size' => '10', 'price' => 199.99],
                    ['size' => '10.5', 'price' => 199.99],
                    ['size' => '11', 'price' => 199.99],
                    ['size' => '11.5', 'price' => 199.99],
                    ['size' => '12', 'price' => 199.99],
                ]);
                break;

            case 'high-heel-pump':
                $this->createShoeDetails($product, 'High Heel Pump', [
                    ['size' => '6', 'price' => 89.99],
                    ['size' => '6.5', 'price' => 89.99],
                    ['size' => '7', 'price' => 89.99],
                    ['size' => '7.5', 'price' => 89.99],
                    ['size' => '8', 'price' => 89.99],
                    ['size' => '8.5', 'price' => 89.99],
                    ['size' => '9', 'price' => 89.99],
                    ['size' => '9.5', 'price' => 89.99],
                    ['size' => '10', 'price' => 89.99],
                ]);
                break;

            // Furniture - Different colors/materials
            case 'modern-sectional-sofa':
                $this->createFurnitureDetails($product, 'Modern Sectional Sofa', [
                    ['variant' => 'Gray Fabric', 'price' => 1299.99],
                    ['variant' => 'Navy Fabric', 'price' => 1299.99],
                    ['variant' => 'Black Leather', 'price' => 1899.99],
                    ['variant' => 'Brown Leather', 'price' => 1899.99],
                ]);
                break;

            case 'platform-bed-frame':
                $this->createFurnitureDetails($product, 'Platform Bed Frame', [
                    ['variant' => 'Oak Finish', 'size' => 'Queen', 'price' => 399.99],
                    ['variant' => 'Oak Finish', 'size' => 'King', 'price' => 499.99],
                    ['variant' => 'Walnut Finish', 'size' => 'Queen', 'price' => 449.99],
                    ['variant' => 'Walnut Finish', 'size' => 'King', 'price' => 549.99],
                ]);
                break;

            // Kitchen - Different sizes
            case 'smart-refrigerator':
                $this->createKitchenDetails($product, 'Smart Refrigerator', [
                    ['variant' => '28 Cubic Feet', 'price' => 2199.99],
                    ['variant' => '30 Cubic Feet', 'price' => 2499.99],
                    ['variant' => '32 Cubic Feet', 'price' => 2799.99],
                ]);
                break;

            case 'stainless-steel-cookware-set':
                $this->createKitchenDetails($product, 'Stainless Steel Cookware Set', [
                    ['variant' => '10-Piece Set', 'price' => 299.99],
                    ['variant' => '12-Piece Set', 'price' => 399.99],
                    ['variant' => '15-Piece Set', 'price' => 499.99],
                ]);
                break;

            // Sports - Different weights/sizes
            case 'adjustable-dumbbells':
                $this->createSportsDetails($product, 'Adjustable Dumbbells', [
                    ['variant' => '5-52.5 lbs', 'price' => 549.99],
                    ['variant' => '5-90 lbs', 'price' => 799.99],
                ]);
                break;

            case 'camping-tent':
                $this->createSportsDetails($product, 'Camping Tent', [
                    ['variant' => '4-Person', 'price' => 149.99],
                    ['variant' => '6-Person', 'price' => 199.99],
                    ['variant' => '8-Person', 'price' => 249.99],
                ]);
                break;

            // Books - Different formats
            case 'the-great-gatsby':
                $this->createBookDetails($product, 'The Great Gatsby', [
                    ['variant' => 'Paperback', 'price' => 12.99],
                    ['variant' => 'Hardcover', 'price' => 19.99],
                    ['variant' => 'E-book', 'price' => 9.99],
                ]);
                break;

            // Beauty - Different sizes
            case 'anti-aging-serum':
                $this->createBeautyDetails($product, 'Anti-Aging Serum', [
                    ['variant' => '30ml', 'price' => 29.99],
                    ['variant' => '50ml', 'price' => 49.99],
                    ['variant' => '100ml', 'price' => 89.99],
                ]);
                break;

            case 'matte-lipstick-set':
                $this->createBeautyDetails($product, 'Matte Lipstick Set', [
                    ['variant' => '6-Piece Set', 'price' => 89.99],
                ]);
                break;

            // Default case for products without specific variants
            default:
                $this->createDefaultDetails($product);
                break;
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
