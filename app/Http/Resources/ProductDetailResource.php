<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'size' => $this->size,
            'color' => $this->color,
            'material' => $this->material,
            'weight' => $this->weight ? $this->weight . ' kg' : null,
            'weight_in_grams' => $this->weight_in_grams ? $this->weight_in_grams . ' g' : null,
            'dimensions' => [
                'length' => $this->length ? $this->length . ' cm' : null,
                'width' => $this->width ? $this->width . ' cm' : null,
                'height' => $this->height ? $this->height . ' cm' : null,
            ],
            'volume' => $this->volume ? $this->volume . ' cm3' : null,
            'origin' => $this->origin,
            'quality' => $this->quality,
            'packaging' => $this->packaging,
            'price' => (float) $this->price,
            'discount' => (float) $this->discount,
            'final_price' => (float) $this->final_price,
            'discount_percentage' => (float) $this->discount_percentage,
            'stock' => (int) $this->stock,
            'min_stock_alert' => (int) $this->min_stock_alert,
            'is_in_stock' => $this->isInStock(),
            'is_low_stock' => $this->isLowStock(),
            'is_out_of_stock' => $this->stock <= 0,
            'sku_variant' => $this->sku_variant,
            'barcode' => $this->barcode,
            'is_active' => (bool) $this->is_active,

            'main_image' => $this->getMainImageUrl(),
            'images_url' => $this->getImagesUrl(),

            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
