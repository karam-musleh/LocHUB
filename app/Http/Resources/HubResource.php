<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HubResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'address_details' => $this->address_details,
            'location' => [
                'id' => $this->location->id,
                'name' => $this->location->name,
                'type' => $this->location->type,
            ],
            'status' => $this->status,
            'owner' => [
                'id' => $this->owner->id,
                'name' => $this->owner->name,
                'email' => $this->owner->email,
            ],
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
