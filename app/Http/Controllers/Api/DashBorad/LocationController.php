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
    public function index(Request $request)
    {
        $request->validate([
            'type' => 'nullable|in:governorate,city,area',
            'parent_id' => 'nullable|exists:locations,id'
        ]);

        $query = Location::query();

        // تحديد النوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // تحديد الأب
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        } else {
            // لو ما في parent_id نجيب الجذر (المحافظات)
            $query->whereNull('parent_id');
        }

        $locations = $query->orderBy('id')->get();

        return $this->successResponse(
            LocationResource::collection($locations),
            'Locations retrieved successfully'
        );
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

    public function update(LocationRequest $request, $slug)
    {
        $location = Location::where('slug', $slug)->first();
        if (!$location) {
            return $this->errorResponse('Location not found', 404);
        }
        $location->update($request->validated());
        return $this->successResponse(new LocationResource($location), 'Location updated successfully');
    }
}
