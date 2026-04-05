<?php

namespace App\Http\Controllers\Api\Front;

use App\Enum\HubStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\HubResource;
use App\Models\Hub;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class HubsController extends Controller
{
    //
    use ApiResponseTrait;
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 6);

        $query = Hub::query()
            ->with([
                'location:id,name',
                'owner:id,name,email',
                'images:id,imageable_id,path,type',
                'services:id,name',
            ])
            ->where('status', HubStatus::APPROVED->value);

        /**
         * SEARCH (fixed OR grouping bug)
         */
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        /**
         * LOCATION FILTER
         */
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        /**
         * SERVICES FILTER
         */
        if ($request->filled('service_ids')) {
            $serviceIds = is_array($request->service_ids)
                ? $request->service_ids
                : explode(',', $request->service_ids);

            $query->whereHas('services', function ($q) use ($serviceIds) {
                $q->whereIn('services.id', $serviceIds);
            });
        }

        /**
         * RATING FILTER (fixed - using AVG)
         */
        if ($request->filled('min_rating')) {
            $query->withAvg('reviews', 'rating')
                ->having('reviews_avg_rating', '>=', $request->min_rating);
        }

        /**
         * PRICE FILTER
         */
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        /**
         * SORTING (secured)
         */
        $sortBy = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');

        $allowedSorts = ['name', 'created_at', 'price'];

        if (in_array($sortBy, $allowedSorts)) {
            $sortOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'desc';

            $query->orderBy($sortBy, $sortOrder);
        }

        /**
         * PAGINATION
         */
        $hubs = $query->paginate($perPage);

        return $this->successResponse([
            'data' => HubResource::collection($hubs),
            'meta' => [
                'current_page' => $hubs->currentPage(),
                'last_page'    => $hubs->lastPage(),
                'total'        => $hubs->total(),
                'per_page'     => $hubs->perPage(),
            ]
        ], 'Hubs retrieved successfully');
    }

    public function show($slug)
    {
        $hub = Hub::with([
            'location',
            'owner',
            'images',
            'services',
            'offers',
            'bookings',
            'reviews',
            'galleryImages',
            'hubSocialAccounts'
        ])
            ->where('slug', $slug)
            ->where('status', HubStatus::APPROVED->value)
            ->firstOrFail();

        return $this->successResponse(
            new HubResource($hub),
            'Hub retrieved successfully'
        );
    }
}
