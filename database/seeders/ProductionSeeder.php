<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws FileCannotBeAdded
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function run()
    {
        // Disable foreign key checks to avoid constraint issues
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Create Laptops category
        $laptopsCategory = Category::create([
            'name' => 'Laptops',
            'description' => 'High-performance laptops for work and gaming',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Add image to category
        $laptopsCategory->addMedia(public_path('images/demo/laptops.jpg'))
            ->toMediaCollection('thumbnail');

        // Create attributes for laptops
        $attributes = [
            [
                'name' => 'Processor',
                'slug' => 'processor',
                'type' => 'text',
                'is_required' => true,
                'is_filterable' => true,
                'is_visible_on_frontend' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Graphics',
                'slug' => 'graphics',
                'type' => 'text',
                'is_required' => true,
                'is_filterable' => true,
                'is_visible_on_frontend' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Display',
                'slug' => 'display',
                'type' => 'text',
                'is_required' => true,
                'is_filterable' => true,
                'is_visible_on_frontend' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Storage',
                'slug' => 'storage',
                'type' => 'text',
                'is_required' => true,
                'is_filterable' => true,
                'is_visible_on_frontend' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'RAM',
                'slug' => 'ram',
                'type' => 'text',
                'is_required' => true,
                'is_filterable' => true,
                'is_visible_on_frontend' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Weight',
                'slug' => 'weight',
                'type' => 'text',
                'is_required' => true,
                'is_filterable' => true,
                'is_visible_on_frontend' => true,
                'sort_order' => 6,
            ],
        ];

        $createdAttributes = [];
        foreach ($attributes as $attributeData) {
            $attribute = Attribute::create($attributeData);
            $createdAttributes[$attribute->slug] = $attribute;
            
            // Attach attribute to category
            $laptopsCategory->attributes()->attach($attribute->id, [
                'is_required' => $attribute->is_required,
                'sort_order' => $attribute->sort_order,
            ]);
        }

        // Create ASUS TUF Gaming F15
        $asusTuf = Product::create([
            'name' => 'ASUS TUF Gaming F15',
            'brand' => 'ASUS',
            'short_description' => 'Powerful gaming laptop with Intel Core i7 and NVIDIA RTX 4070',
            'description' => 'The ASUS TUF Gaming F15 is a powerful gaming laptop featuring the latest Intel Core i7-12700H processor and NVIDIA GeForce RTX 4070 graphics. With a 15.6" Full HD 144Hz display, 2TB SSD, and 32GB DDR5 RAM, this laptop delivers exceptional gaming and multitasking performance.',
            'sku' => 'ASUS-TUF-F15-001',
            'is_active' => true,
        ]);

        // Attach to category
        $asusTuf->categories()->attach($laptopsCategory->id);

        // Add product details
        $asusTufBlackDetail = ProductDetail::create([
            'product_id' => $asusTuf->id,
            'color' => 'Black',
            'price' => 1499.99,
            'discount' => 0.00,
            'stock' => 15,
            'min_stock_alert' => 3,
            'sku_variant' => 'ASUS-TUF-F15-BK-001',
            'is_active' => true,
        ]);
        // Add product details
        $asusTufGrayDetail = ProductDetail::create([
            'product_id' => $asusTuf->id,
            'color' => 'Gray',
            'price' => 1600,
            'discount' => 0.00,
            'stock' => 15,
            'min_stock_alert' => 3,
            'sku_variant' => 'ASUS-TUF-F15-BK-002',
            'is_active' => true,
        ]);

        // Add product images for ASUS TUF
        $asusImagesBlack = [
            '4-33-300x259.jpg',
            '3-32-300x203.jpg',
        ];
        // Add product images for ASUS TUF
        $asusImagesGray = [
            '51dBJc-pNcL._AC_SL1500_.jpg',
            '2-33-300x205.jpg',
        ];

        foreach ($asusImagesBlack as $imageUrl) {
            $asusTufBlackDetail->addMedia(public_path('images/demo/' . $imageUrl))
                ->toMediaCollection('images');
        }

        foreach ($asusImagesGray as $imageUrl) {
            $asusTufGrayDetail->addMedia(public_path('images/demo/' . $imageUrl))
                ->toMediaCollection('images');
        }

        // Add attribute values for ASUS TUF
        $this->createAttributeValue($asusTufBlackDetail->id, $createdAttributes['processor']->id, 'Intel Core i7-12700H');
        $this->createAttributeValue($asusTufBlackDetail->id, $createdAttributes['graphics']->id, 'NVIDIA GeForce RTX 4070 (Laptop, 140W)');
        $this->createAttributeValue($asusTufBlackDetail->id, $createdAttributes['display']->id, '15.6", Full HD (1920 x 1080), 144 Hz, IPS + G-Sync');
        $this->createAttributeValue($asusTufBlackDetail->id, $createdAttributes['storage']->id, '2000GB SSD');
        $this->createAttributeValue($asusTufBlackDetail->id, $createdAttributes['ram']->id, '32GB DDR5');
        $this->createAttributeValue($asusTufBlackDetail->id, $createdAttributes['weight']->id, '2.20 kg (4.9 lbs)');

        $this->createAttributeValue($asusTufGrayDetail->id, $createdAttributes['processor']->id, 'Intel Core i7-12700H');
        $this->createAttributeValue($asusTufGrayDetail->id, $createdAttributes['graphics']->id, 'NVIDIA GeForce RTX 4070 (Laptop, 140W)');
        $this->createAttributeValue($asusTufGrayDetail->id, $createdAttributes['display']->id, '15.6", Full HD (1920 x 1080), 144 Hz, IPS + G-Sync');
        $this->createAttributeValue($asusTufGrayDetail->id, $createdAttributes['storage']->id, '2000GB SSD');
        $this->createAttributeValue($asusTufGrayDetail->id, $createdAttributes['ram']->id, '32GB DDR5');
        $this->createAttributeValue($asusTufGrayDetail->id, $createdAttributes['weight']->id, '2.20 kg (4.9 lbs)');

        // Create ASUS Vivobook 15
        $asusVivobook = Product::create([
            'name' => 'ASUS Vivobook 15',
            'brand' => 'ASUS',
            'short_description' => 'Sleek and powerful everyday laptop with Intel Core i5',
            'description' => 'The ASUS Vivobook 15 is a sleek and lightweight laptop featuring an Intel Core i5-1334U processor, Intel Iris Xe Graphics, and a 15.6" Full HD IPS display. With 16GB RAM and a 512GB SSD, it offers excellent performance for work and entertainment.',
            'sku' => 'ASUS-VIVO-001',
            'is_active' => true,
        ]);

        // Attach to category
        $asusVivobook->categories()->attach($laptopsCategory->id);

        // Add product details
        $vivobookDetail = ProductDetail::create([
            'product_id' => $asusVivobook->id,
            'color' => 'Silver',
            'price' => 899.99,
            'discount' => 0.00,
            'stock' => 25,
            'min_stock_alert' => 5,
            'sku_variant' => 'ASUS-VIVO-SL-001',
            'is_active' => true,
        ]);

        // Add product images for ASUS Vivobook
        $vivobookImages = [
            '10-e1678205122279-300x194.jpg',
            '9-e1678205097129-300x198.jpg',
            '8-e1678205071286-300x197.jpg',
        ];

        foreach ($vivobookImages as $imageUrl) {
            $vivobookDetail->addMedia(public_path('images/demo/' . $imageUrl))
                ->toMediaCollection('images');
        }

        // Add attribute values for ASUS Vivobook
        $this->createAttributeValue($vivobookDetail->id, $createdAttributes['processor']->id, 'Intel Core i5-1334U');
        $this->createAttributeValue($vivobookDetail->id, $createdAttributes['graphics']->id, 'Intel Iris Xe Graphics G7 (80EU)');
        $this->createAttributeValue($vivobookDetail->id, $createdAttributes['display']->id, '15.6", Full HD (1920 x 1080), IPS');
        $this->createAttributeValue($vivobookDetail->id, $createdAttributes['storage']->id, '512GB SSD');
        $this->createAttributeValue($vivobookDetail->id, $createdAttributes['ram']->id, '16GB RAM');
        $this->createAttributeValue($vivobookDetail->id, $createdAttributes['weight']->id, '1.70 kg (3.7 lbs)');

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Helper method to create attribute values
     *
     * @param int $productId
     * @param int $attributeId
     * @param string $value
     * @return void
     */
    private function createAttributeValue(int $productDetailId, int $attributeId, string $value): void
    {
        AttributeValue::create([
            'product_detail_id' => $productDetailId,
            'attribute_id' => $attributeId,
            'value' => $value,
            'value_type' => 'string',
        ]);
    }
}
