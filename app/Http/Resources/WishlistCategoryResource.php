<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WishlistCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'is_default' => $this->is_default,
            'icon_url' => $this->getIconUrl(),
            'image_url' => $this->getImageUrl(),
            'item_count' => $this->item_count,
            'is_default_category' => $this->isDefault(),
            'is_custom_category' => $this->isCustom(),
            'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),
            'items' => $this->whenLoaded('items', function () {
                return WishlistItemResource::collection($this->items);
            }),
            'products' => $this->whenLoaded('products', function () {
                return ProductResource::collection($this->products);
            }),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
