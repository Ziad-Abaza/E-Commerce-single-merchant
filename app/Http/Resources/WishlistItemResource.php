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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 