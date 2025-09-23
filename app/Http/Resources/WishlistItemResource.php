<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WishlistItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'wishlist_category_id' => $this->wishlist_category_id,
            'product_id' => $this->product_id,
            'is_product_in_stock' => $this->isProductInStock(),
            'has_product_discount' => $this->hasProductDiscount(),
            'lowest_price' => $this->lowest_price,
            'highest_price' => $this->highest_price,
            'lowest_final_price' => $this->lowest_final_price,
            'highest_final_price' => $this->highest_final_price,
            'category' => $this->whenLoaded('category', function () {
                return new WishlistCategoryResource($this->category);
            }),
            'product' => $this->whenLoaded('product', function () {
                return new ProductResource($this->product);
            }),
            'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
