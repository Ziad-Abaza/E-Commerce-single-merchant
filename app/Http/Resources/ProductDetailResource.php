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
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        // $this now refers to the main Product model passed from the controller

        return [
            // 1. Main Product Information
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

            // 2. **THE FIX**: Embed the array of all variants (details).
            // We create a simple sub-resource here on-the-fly for each detail.
            'details' => $this->whenLoaded('details', function () {
                return $this->details->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'product_id' => $detail->product_id,
                        'size' => $detail->size,
                        'color' => $detail->color,
                        'material' => $detail->material,
                        'weight' => $detail->weight ? $detail->weight . ' kg' : null,
                        'weight_in_grams' => $detail->weight_in_grams ? $detail->weight_in_grams . ' g' : null,
                        'dimensions' => [
                            'length' => $detail->length ? $detail->length . ' cm' : null,
                            'width' => $detail->width ? $detail->width . ' cm' : null,
                            'height' => $detail->height ? $detail->height . ' cm' : null,
                        ],
                        'price' => (float) $detail->price,
                        'discount' => (float) $detail->discount,
                        'final_price' => (float) $detail->final_price, // Assumes accessor on ProductDetail model
                        'stock_quantity' => (int) $detail->stock,
                        'sku_variant' => $detail->sku_variant,
                        'is_active' => (bool) $detail->is_active,



                        'volume' => $detail->volume ? $detail->volume . ' cm3' : null,
                        'origin' => $detail->origin,
                        'quality' => $detail->quality,
                        'packaging' => $detail->packaging,
                        'discount_percentage' => (float) $detail->discount_percentage,
                        'stock' => (int) $detail->stock,
                        'is_out_of_stock' => $detail->stock <= 0,
                        'barcode' => $detail->barcode,
                        'main_image' => $detail->getMainImageUrl(),
                        'images_url' => $detail->getImagesUrl(),
                        'created_at' => $detail->created_at?->toDateTimeString(),
                        'updated_at' => $detail->updated_at?->toDateTimeString(),
                    ];
                });
            }),
        ];
    }
}