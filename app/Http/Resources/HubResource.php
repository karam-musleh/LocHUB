<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HubResource extends JsonResource
{
    private function getLocationBreadcrumb($lang): array
    {
        if (!$this->location) {
            return [];
        }

        $breadcrumb = [];
        $current = $this->location;
        $depth = 0;

        while ($current && $depth < 10) {
            array_unshift($breadcrumb, [
                'id' => $current->id,
                'name' => $current->getTranslation('name', $lang),
                'type' => $current->type->value ?? $current->type,
            ]);
            $current = $current->parent ?? null;
            $depth++;
        }

        return $breadcrumb;
    }

    public function toArray($request): array
    {
        $lang = request()->query('lang', app()->getLocale());

        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', $lang),
            'slug' => $this->slug,
            'description' => $this->getTranslation('description', $lang),
            'address_details' => $this->getTranslation('address_details', $lang),

            'location' => $this->whenLoaded('location', function () use ($lang) {
                return [
                    'id' => $this->location->id,
                    'name' => $this->location->getTranslation('name', $lang),
                    'type' => $this->location->type->value ?? $this->location->type,
                    'breadcrumb' => $this->getLocationBreadcrumb($lang),
                ];
            }),

            'images' => [
                'main' => $this->main_image_url,
                'gallery' => $this->when(
                    $this->relationLoaded('images'),
                    fn() => $this->getGalleryImagesWithIds()
                ),
            ],

            'services' => $this->when(
                $this->relationLoaded('services'),
                fn() => $this->services->map(function ($service) use ($lang) {
                    return [
                        'id' => $service->id,
                        'name' => $service->getTranslation('name', $lang),
                        'description' => $service->getTranslation('description', $lang),
                        'is_global' => true,
                    ];
                })
            ),

            'custom_services' => $this->when(
                $this->relationLoaded('customServices'),
                fn() => $this->customServices->map(function ($service) use ($lang) {
                    return [
                        'id' => $service->id,
                        'name' => $service->getTranslation('name', $lang),
                        'description' => $service->getTranslation('description', $lang),
                        'is_global' => false,
                    ];
                })
            ),

            'all_services' => $this->when(
                $this->relationLoaded('services') && $this->relationLoaded('customServices'),
                fn() => collect($this->services)
                    ->merge($this->customServices)
                    ->map(function ($service) use ($lang) {
                        return [
                            'id' => $service->id,
                            'name' => $service->getTranslation('name', $lang),
                            'description' => $service->getTranslation('description', $lang),
                            'is_global' => $service->is_global,
                            'is_custom' => $service->hub_id !== null,
                        ];
                    })
            ),
            'offers' => $this->when(
                $this->relationLoaded('offers'),
                fn() => OfferResource::collection($this->offers)
            ),

            'contact' => $this->contact,
            'hourly_price' => $this->hourly_price,
            'working_hours' => [
                'start' => $this->working_hours_start ? $this->working_hours_start->format('H:i') : null,
                'end' => $this->working_hours_end ? $this->working_hours_end->format('H:i') : null,
            ],
            // 'hasOffer'
            'reviews' => [
                'count' => $this->reviewCount(),
                'average_rating' => $this->averageRating(),
            ],
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'featured_priority' => $this->featured_priority,
            'featured_from' => $this->featured_from,
            'featured_until' => $this->featured_until,

            'rejection_reason' => $this->rejection_reason,

            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id' => $this->owner->id,
                    'name' => $this->owner->name,
                    'email' => $this->owner->email,
                ];
            }),

            'social_accounts' => $this->when(
                $this->relationLoaded('hubSocialAccounts'),
                fn() => $this->hubSocialAccounts->map(function ($account) {
                    return [
                        'id' => $account->id,
                        'platform' => $account->platform,
                        'url' => $account->url,
                    ];
                })
            ),
        ];
    }
}
