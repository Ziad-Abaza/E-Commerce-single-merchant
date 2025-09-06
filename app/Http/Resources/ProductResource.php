<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'brand' => $this->brand,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'sku' => $this->sku,
            'is_active' => $this->is_active,
            'main_image_url' => $this->getMainImageUrl(),
            'gallery_images' => $this->getImagesUrl(),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'price' => $this->details->min('final_price'),
        ];
    }
}
