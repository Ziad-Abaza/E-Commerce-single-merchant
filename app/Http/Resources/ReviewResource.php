<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
            'rating' => $this->rating,
            'title' => $this->title,
            'comment' => $this->comment,
            'is_verified_purchase' => $this->is_verified_purchase,
            'is_active' => $this->active,
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
