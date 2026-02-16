<?php

namespace App\Http\Controllers\Api\Hubs;

use App\Models\Hub;
use Illuminate\Support\Facades\Log;
use App\Enum\HubStatus;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\HubRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\HubResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HubController extends Controller
{
    use ApiResponseTrait;
    //
    public function myHubs()
    {
        $user = Auth::guard('api')->user();

        $hubs = Hub::with('location', 'owner', 'images')
            ->where('owner_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->successResponse(HubResource::collection($hubs), 'My Hubs retrieved successfully');
    }

    public function store(HubRequest $request)
    {
        // dd('here');
        $user = Auth::guard('api')->user();
        $hubData = $request->validated();
        $hubData['owner_id'] = $user->id;
        $hubData['status'] = HubStatus::PENDING->value;
        $hub = Hub::create($hubData);
        // رفع الصورة الرئيسية
        if ($request->hasFile('main_image')) {
            ImageHelper::uploadImage($hub, $request->file('main_image'), 'hubs/main', 'main', 'custom');
        }

        // رفع معرض الصور
        if ($request->hasFile('gallery')) {
            $images = [];

            foreach ($request->file('gallery') as $file) {
                $path = $file->store('hubs/gallery', 'custom');

                $image = $hub->images()->create([
                    'path' => $path,
                    'type' => 'gallery',
                ]);

                $images[] = $image;
            }
        }
        // إضافة الحسابات الاجتماعية
        if (!empty($hubData['social_accounts'])) {
            $hub->socialAccounts()->createMany($hubData['social_accounts']);
        }
        dd($hub->socialAccounts);


        $hub->load('images', 'services', 'offers', 'bookings', 'reviews', 'location', 'owner', 'galleryImages', 'socialAccounts');
        // dd($hub);
        return $this->successResponse(new HubResource($hub), 'Hub created successfully', 201);
    }

    public function show($slug)
    {
        $hub = Hub::with('location', 'owner', 'services', 'offers', 'bookings', 'reviews', 'images')
            ->where('slug', $slug)
            ->first();

        if (!$hub) {
            return $this->errorResponse('Hub not found', 404);
        }

        return $this->successResponse(new HubResource($hub), 'Hub retrieved successfully');
    }
    public function update(HubRequest $request, $slug)
    {
        try {
            DB::beginTransaction();

            $user = Auth::guard('api')->user();
            $hub = Hub::where('slug', $slug)->first();

            if (!$hub) {
                return $this->errorResponse('Hub not found', 404);
            }

            if ($hub->owner_id !== $user->id) {
                return $this->errorResponse('You are not authorized to update this hub', 403);
            }

            $hub->update($request->validated());

            // تحديث الصورة الرئيسية
            if ($request->hasFile('main_image')) {
                ImageHelper::updateImage($hub, $request->file('main_image'), 'hubs/main', 'main', 'custom');
            }

            // تحديث معرض الصور
            if ($request->has('delete_gallery_ids') || $request->hasFile('gallery_images')) {
                ImageHelper::updateGallery(
                    $hub,
                    $request->file('gallery_images', []),
                    $request->input('delete_gallery_ids', []),
                    'hubs/gallery',
                    'custom'
                );
            }

            DB::commit();

            $hub->load('images', 'location', 'owner');

            return $this->successResponse(new HubResource($hub), 'Hub updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(
                'Failed to update hub',
                500,
                ['error' => $e->getMessage()]
            );
        }
    }



    public function destroy($slug)
    {
        $user = Auth::guard('api')->user();

        $hub = Hub::where('slug', $slug)->first();
        if (!$hub) {
            return $this->errorResponse('Hub not found', 404);
        }

        // تحقق من ملكية الهب
        if ($hub->owner_id !== $user->id) {
            return $this->errorResponse('You are not authorized to delete this hub', 403);
        }

        // حذف كل الصور المرتبطة بالهب
        ImageHelper::deleteAll($hub);

        // حذف الهب نفسه
        $hub->delete();

        return $this->successResponse(null, 'Hub deleted successfully');
    }



    // change hub status for admin
    public function changeStatus(Request $request, $hubId)
    {
        $request->validate([
            'status' => ['required', 'in:' . implode(',', array_map(fn($status) => $status->value, HubStatus::cases()))],
            'rejection_reason' => ['nullable', 'string', 'required_if:status,' . HubStatus::REJECTED->value],

        ]);

        $hub = Hub::find($hubId);

        if (!$hub) {
            return $this->errorResponse('Hub not found', 404);
        }

        $hub->status = $request->status;
        $hub->rejection_reason = $request->status === 'rejected' ? $request->rejection_reason : null;
        $hub->save();
        return $this->successResponse(
            new HubResource($hub),
            "Hub status changed to {$hub->status}"
        );
    }
}
