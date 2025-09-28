<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// This resource is now designed to format the entire Product Page
class ProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>~
     */
    public function toArray($request)
    {
        return [
            // Main Product Information
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'brand' => $this->brand,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'sku' => $this->sku,
            'main_image_url' => $this->getMainImageUrl(), // Assumes method exists on Product model
            'gallery_images' => $this->getImagesUrl(),   // Assumes method exists on Product model
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'reviews_avg_rating' => $this->reviews_avg_rating,
            'reviews_count' => $this->reviews_count,

            // We create a simple sub-resource here on-the-fly for each detail.
            'details' => $this->whenLoaded('details', function () {
                return $this->details->map(function ($detail) {
                    $attributes = [];
                    
                    if ($detail->relationLoaded('attributeValues')) {
                        $attributes = $detail->attributeValues->map(function ($attributeValue) {
                            // Clean up the value by removing escaped quotes if present
                            $value = is_string($attributeValue->value) 
                                ? stripslashes(trim($attributeValue->value, '"\'')) 
                                : $attributeValue->value;
                                
                            return [
                                'id' => $attributeValue->attribute_id,
                                'name' => $attributeValue->attribute->name ?? null,
                                'type' => $attributeValue->attribute->type ?? null,
                                'value' => $value,
                                'is_visible' => $attributeValue->attribute->is_visible_on_frontend ?? false,
                                'is_variant' => $attributeValue->attribute->is_variant ?? false,
                                'is_filterable' => $attributeValue->attribute->is_filterable ?? false,
                            ];
                        })->toArray();
                    }

                    return [
                        'id' => $detail->id,
                        'product_id' => $detail->product_id,
                        'color' => $detail->color,
                        'price' => (float) $detail->price,
                        'discount' => (float) $detail->discount,
                        'final_price' => (float) $detail->final_price, 
                        'stock_quantity' => (int) $detail->stock,
                        'sku_variant' => $detail->sku_variant,
                        'is_active' => (bool) $detail->is_active,
                        'discount_percentage' => (float) $detail->discount_percentage,
                        'stock' => (int) $detail->stock,
                        'is_out_of_stock' => $detail->stock <= 0,
                        'main_image' => $detail->getMainImageUrl(),
                        'images_url' => $detail->getImagesUrl(),
                        'attributes' => $attributes,
                        'created_at' => $detail->created_at?->toDateTimeString(),
                        'updated_at' => $detail->updated_at?->toDateTimeString(),
                    ];
                });
            }),
        ];
    }
}