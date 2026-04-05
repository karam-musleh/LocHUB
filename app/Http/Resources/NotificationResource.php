<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => class_basename($this->type),

            'data' => [
                'hub_id' => $this->data['hub_id'] ?? null,
                'hub_name' => $this->data['hub_name'] ?? null,
                'owner_name' => $this->data['owner_name'] ?? null,
                'message' => $this->data['message'] ?? null,
            ],

            'read_at' => $this->read_at,
            'is_read' => !is_null($this->read_at),

            'created_at' => $this->created_at?->diffForHumans(),
        ];
    }
}
