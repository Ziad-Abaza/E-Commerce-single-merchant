<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $attributes = [];
        
        // Load the attribute values relationship if not already loaded
        if ($this->relationLoaded('attributeValues')) {
            $attributes = $this->attributeValues->groupBy(function ($attributeValue) {
                return $attributeValue->attribute->name;
            })->map(function ($values, $attributeName) {
                return [
                    'name' => $attributeName,
                    'slug' => $values->first()->attribute->slug ?? null,
                    'type' => $values->first()->attribute->type ?? null,
                    'values' => $values->map(function ($value) {
                        return [
                            'id' => $value->id,
                            'value' => is_string($value->value) 
                                ? stripslashes(trim($value->value, '\"\'')) 
                                : $value->value,
                            'is_visible' => $value->attribute->is_visible_on_frontend ?? false,
                            'is_variant' => $value->attribute->is_variant ?? false,
                            'is_filterable' => $value->attribute->is_filterable ?? false,
                        ];
                    })->toArray()
                ];
            })->values()->toArray();
        }

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'color' => $this->color,
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
            'is_active' => (bool) $this->is_active,
            'main_image' => $this->getMainImageUrl(),
            'images_url' => $this->getImagesUrl(),
            'attributes' => $attributes,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
