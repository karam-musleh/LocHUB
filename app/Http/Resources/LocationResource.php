<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
            'type' => $this->type,
            'children' => $this->when(
                ($this->relationLoaded('children') && $this->children->isNotEmpty()),
                fn() => LocationResource::collection($this->children)
            ),
        ];
    }
}
