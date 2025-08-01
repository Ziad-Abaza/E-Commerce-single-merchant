<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'thumbnail_url' => $this->getThumbnailUrl(),
            'icon_url' => $this->getIconUrl(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
