<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
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
            'type' => $this->type,
            'options' => $this->options,
            'is_filterable' => $this->is_filterable,
            'is_visible_on_frontend' => $this->is_visible_on_frontend,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            
            // This will include the pivot data from the attribute_category table
            // only when the attribute is loaded through a category relationship.
            'pivot' => $this->whenPivotLoaded('attribute_category', function () {
                return [
                    'is_required' => $this->pivot->is_required,
                    'sort_order' => $this->pivot->sort_order,
                ];
            }),
        ];
    }
}