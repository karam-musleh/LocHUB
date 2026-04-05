<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Http\Resources\ServiceResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ServiceController extends Controller
{
    use ApiResponseTrait, AuthorizesRequests;

    public function index()
    {
        // $lang = request()->query('lang', app()->getLocale());
        $per_page = request()->query('per_page', 15);

        $services = Service::where('is_active', true)
            ->latest()
            ->paginate($per_page);

        return $this->successResponse(
            ServiceResource::collection($services),
            'Services fetched successfully'
        );
    }

    public function store(ServiceRequest $request)
    {
        // dd($request->validated());

        $service = Service::create($request->validated());


        return $this->successResponse(
            new ServiceResource($service),
            'Service created successfully',
            201
        );
    }


    public function show($id)
    {
        $lang = request()->query('lang', app()->getLocale());

        $service = Service::findOrFail($id);

        if (!$service->is_active) {
            return $this->errorResponse('Service not found', 404);
        }

        return $this->successResponse(
            new ServiceResource($service),
            'Service fetched successfully'
        );
    }

    // Admin فقط - تحديث الخدمة
    public function update(ServiceRequest $request, $id)
    {

        $service = Service::findOrFail($id);
        $service->update($request->validated());

        return $this->successResponse(
            new ServiceResource($service),
            'Service updated successfully'
        );
    }

    // Admin فقط - حذف الخدمة
    public function destroy($id)
    {

        $service = Service::findOrFail($id);
        $service->delete();

        return $this->successResponse(
            null,
            'Service deleted successfully'
        );
    }
}
