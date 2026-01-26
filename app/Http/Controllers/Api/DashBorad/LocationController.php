<?php

namespace App\Http\Controllers\Api\DashBorad;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Traits\ApiResponserTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\LocationRequest;
use App\Http\Resources\LocationResource;

class LocationController extends Controller
{
    use ApiResponserTrait;
    //index
    public function index()
    {

    }

    //store
    public function store(LocationRequest $request)
    {
        $location = Location::create($request->validated());
        return $this->successResponse(new LocationResource($location), 'Location created successfully', 201);

    }
    //show
    public function show($slug)
    {
        $location = Location::with('children')->where('slug', $slug)->first();
        if (!$location) {
            return $this->errorResponse('Location not found', 404);
        }
        return $this->successResponse(new LocationResource($location), 'Location retrieved successfully');
    }
}
