<?php

namespace App\Http\Controllers\Api\DashBoard;

use App\Models\Location;
use Illuminate\Http\Request;
// use App\Traits\ApiResponserTrait;
use App\Traits\ApiResponseTrait;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocationRequest;
use App\Http\Resources\LocationResource;

class LocationController extends Controller
{
    use ApiResponseTrait;


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
        }


        $locations = $query->with('children')->orderBy('id')->get();

        return $this->successResponse(
            LocationResource::collection($locations),
            'Locations retrieved successfully'
        );
    }


    //store
    public function store(LocationRequest $request)
    {
        $location = Location::create($request->validated());
        // dd($location);
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
    public function destroy($slug)
    {
        $location = Location::where('slug', $slug)->first();
        if (!$location) {
            return $this->errorResponse('Location not found', 404);
        }
        $location->delete();
        return $this->successResponse(null, 'Location deleted successfully');
    }
}
