<?php

namespace App\Http\Controllers\Api\DashBoard;

use App\Models\Hub;
use App\Models\Offer;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\OfferRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\OfferResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OfferController extends Controller
{
    use ApiResponseTrait, AuthorizesRequests;
    public function index(Hub $hub)
    {
        $perPage = request()->query('per_page', 10);

        $offers = $hub->offers()->latest()->paginate($perPage);

        return $this->successResponse(
            OfferResource::collection($offers),
            'Offers fetched successfully'
        );
    }
    public function store(Hub $hub, OfferRequest $request)
    {
        $this->authorize('create', [Offer::class, $hub]);

        $offer = $hub->offers()->create($request->validated());

        return $this->successResponse(
            new OfferResource($offer),
            'Offer created successfully',
            201
        );
    }
    public function show(Hub $hub, Offer $offer)
    {

        return $this->successResponse(
            new OfferResource($offer),
            'Offer fetched successfully'
        );
    }
    public function update(Hub $hub, Offer $offer, OfferRequest $request)
    {
        $this->authorize('update', $offer);

        $offer->update($request->validated());

        return $this->successResponse(
            new OfferResource($offer),
            'Offer updated successfully'
        );
    }

    /**
     * حذف عرض
     */
    public function destroy(Hub $hub, Offer $offer)
    {
        $this->authorize('delete', $offer);

        $offer->delete();

        return $this->successResponse(
            null,
            'Offer deleted successfully'
        );
    }
}
