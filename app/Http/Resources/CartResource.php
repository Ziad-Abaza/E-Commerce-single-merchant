<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->productDetail->product->name,
            'product_detail_id' => $this->product_detail_id,
            'quantity' => $this->quantity,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'product_detail' => $this->whenLoaded('productDetail', function () {
                return [
                    'id' => $this->productDetail->id,
                    'product_id' => $this->productDetail->product_id,
                    'size' => $this->productDetail->size,
                    'color' => $this->productDetail->color,
                    'material' => $this->productDetail->material,
                    'weight' => $this->productDetail->weight,
                    'weight_in_grams' => $this->productDetail->weight_in_grams,
                    'dimensions' => [
                        'length' => $this->productDetail->length,
                        'width' => $this->productDetail->width,
                        'height' => $this->productDetail->height,
                    ],
                    'volume' => $this->productDetail->volume,
                    'origin' => $this->productDetail->origin,
                    'quality' => $this->productDetail->quality,
                    'packaging' => $this->productDetail->packaging,
                    'price' => $this->productDetail->price,
                    'discount' => $this->productDetail->discount,
                    'final_price' => $this->productDetail->final_price,
                    'discount_percentage' => $this->productDetail->discount_percentage,
                    'stock' => $this->productDetail->stock,
                    'min_stock_alert' => $this->productDetail->min_stock_alert,
                    'is_in_stock' => $this->productDetail->isInStock(),
                    'is_low_stock' => $this->productDetail->isLowStock(),
                    'is_out_of_stock' => $this->productDetail->isOutOfStock(),
                    'sku_variant' => $this->productDetail->sku_variant,
                    'barcode' => $this->productDetail->barcode,
                    'is_active' => $this->productDetail->is_active,
                    'main_image' => $this->productDetail->getMainImageUrl(),
                    'images_url' => $this->productDetail->getImagesUrl(),
                    'created_at' => $this->productDetail->created_at?->toDateTimeString(),
                    'updated_at' => $this->productDetail->updated_at?->toDateTimeString(),
                    'product' => $this->whenLoaded('productDetail.product', function () {
                        return [
                            'id' => $this->productDetail->product->id,
                            'name' => $this->productDetail->product->name,
                            'slug' => $this->productDetail->product->slug,
                            'brand' => $this->productDetail->product->brand,
                            'short_description' => $this->productDetail->product->short_description,
                            'description' => $this->productDetail->product->description,
                            'sku' => $this->productDetail->product->sku,
                            'is_active' => $this->productDetail->product->is_active,
                            'main_image_url' => $this->productDetail->product->getMainImageUrl(),
                            'gallery_images' => $this->productDetail->product->getImagesUrl(),
                            'created_at' => $this->productDetail->product->created_at?->toDateTimeString(),
                            'updated_at' => $this->productDetail->product->updated_at?->toDateTimeString(),
                        ];
                    }),
                ];
            }),
            'total_price' => $this->total_price ?? ($this->productDetail->final_price * $this->quantity),
        ];
    }
}
