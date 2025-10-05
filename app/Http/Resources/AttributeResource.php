<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'options' => $this->options,
            'is_required' => (bool) $this->is_required,
            'is_filterable' => (bool) $this->is_filterable,
            'is_variant' => (bool) $this->is_variant,
            'is_visible_on_frontend' => (bool) $this->is_visible_on_frontend,
            'sort_order' => (int) $this->sort_order,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'categories' => $this->whenLoaded('categories', function () {
                return $this->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'pivot' => [
                            'is_required' => (bool) $category->pivot->is_required,
                            'sort_order' => (int) $category->pivot->sort_order,
                        ],
                    ];
                });
            }),
        ];
    }
}
