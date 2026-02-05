<?php

namespace App\Http\Controllers\Api;

use App\Models\Hub;
use App\Models\Service;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Http\Resources\ServiceResource;

class ServiceController extends Controller
{
    use ApiResponseTrait;

    public function index(Hub $hub)
    {
    $per_page = request()->query('per_page', 10); // عدد العناصر في الصفحة، افتراضي 10
    // للـ performance: استخدم pagination للـ hubs اللي فيها خدمات كثيرة
        $services = $hub->services()
            ->latest()
            ->paginate($per_page);

        return $this->successResponse(
            ServiceResource::collection($services),
            'Services fetched successfully'
        );
    }

    public function store(Hub $hub, ServiceRequest $request)
    {
        $service = $hub->services()->create($request->validated());

        return $this->successResponse(
            new ServiceResource($service),
            'Service created successfully',
            201
        );
    }

    public function show(Hub $hub, Service $service)
    {
        // مع scoped bindings، Laravel بتتأكد تلقائياً
        // إن الـ service تابع للـ hub (404 إذا مش تابع)

        return $this->successResponse(
            new ServiceResource($service),
            'Service fetched successfully'
        );
    }

    public function update(Hub $hub, Service $service, ServiceRequest $request)
    {
        $service->update($request->validated());

        return $this->successResponse(
            new ServiceResource($service),
            'Service updated successfully'
        );
    }

    public function destroy(Hub $hub, Service $service)
    {
        $service->delete();

        return $this->successResponse(
            null,
            'Service deleted successfully'
        );
    }
}
