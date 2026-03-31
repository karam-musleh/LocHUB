<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lang = request()->query('lang', app()->getLocale());

        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'role'           => $this->role,
            'location' => $this->whenLoaded('location', function () use ($lang) {
                return [
                    'id' => $this->location->id,
                    'name' => $this->location->getTranslation('name', $lang),
                    'type' => $this->location->type,
                    
                ];
            }),
            'specialization' => $this->specialization,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
