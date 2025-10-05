<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryWithAttributesResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'slug' => $this->slug,
      'description' => $this->description,
      'is_active' => (bool) $this->is_active,
      'sort_order' => (int) $this->sort_order,
      'parent_id' => $this->parent_id,
      'attributes' => $this->whenLoaded('attributes', function () {
        return $this->attributes->map(function ($attr) {
          return [
            'id' => $attr->id,
            'name' => $attr->name,
            'slug' => $attr->slug,
            'type' => $attr->type,
            'is_required_in_category' => (bool) $attr->pivot->is_required,
            'sort_order_in_category' => (int) $attr->pivot->sort_order,
          ];
        });
      }),
      'created_at' => $this->created_at?->toDateTimeString(),
      'updated_at' => $this->updated_at?->toDateTimeString(),
    ];
  }
}
