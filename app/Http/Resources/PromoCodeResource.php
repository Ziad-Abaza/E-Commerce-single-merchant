<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromoCodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'discount_type' => $this->discount_type,
            'discount_value' => (float) $this->discount_value,
            'target_type' => $this->target_type,
            'total_usage_limit' => $this->total_usage_limit,
            'per_user_usage_limit' => $this->per_user_usage_limit,
            'total_usage_count' => $this->total_usage_count,
            'start_date' => $this->start_date?->toDateTimeString(),
            'end_date' => $this->end_date?->toDateTimeString(),
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'products' => $this->whenLoaded('products', function () {
                return $this->products->map(fn($product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                ]);
            }),
            'categories' => $this->whenLoaded('categories', function () {
                return $this->categories->map(fn($category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                ]);
            }),
            'usages_count' => $this->whenCounted('usages'),
        ];
    }
}
