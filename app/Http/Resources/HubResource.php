<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HubResource extends JsonResource
{
    public function toArray($request): array
    {

        $lang = request()->query('lang', app()->getLocale());

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            // 'name' => $this->getTranslation('name', $lang),
            'name' => $this->getTranslation('name', $lang),
            'description' => $this->getTranslation('description', $lang),
            'address_details' => $this->getTranslation('address_details' , $lang),
            'location' => [
                'id' => $this->location->id,
                'name' => $this->location->getTranslation('name', $lang),
                'type' => $this->location->type,
            ],

            "images" => [
                "main" => $this->main_image_url,
                "gallery" => $this->when(
                    $this->relationLoaded('galleryImages'),
                    fn() => $this->gallery_images_urls
                ),
            ],
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'owner' => [
                'id' => $this->owner->id,
                'name' => $this->owner->name,
                'email' => $this->owner->email,
            ],
            'social_Accounts' => SocialResource::collection($this->whenLoaded('socialAccounts')),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
