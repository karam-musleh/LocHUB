<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
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
            'id' => $this->id,
            'hub_id' => $this->hub_id,
            'title' => $this->getTranslation('title', $lang),
            'description' => $this->getTranslation('description', $lang),
            'type' => $this->type,
            'price' => $this->price,
            'duration' => $this->duration,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
