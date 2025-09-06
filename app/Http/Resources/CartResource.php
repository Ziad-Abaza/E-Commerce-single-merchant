<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'user_id' => $this->user_id,
            'product_detail_id' => $this->product_detail_id,
            'quantity' => $this->quantity,
            'total_price' => $this->total_price,
            'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),
            'product_detail' => $this->whenLoaded('productDetail', function () {
                return new ProductDetailResource($this->productDetail);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 