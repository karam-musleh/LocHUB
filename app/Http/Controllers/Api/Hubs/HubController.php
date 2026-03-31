<?php

namespace App\Http\Controllers\Api\Hubs;

use App\Models\Hub;
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


        if ($request->has('service_ids') && !empty($request->input('service_ids'))) {
            $hub->services()->attach($request->input('service_ids'));
        }
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
            $hub->hubSocialAccounts()->createMany($hubData['social_accounts']);
        }
        $hub->load('images', 'services', 'offers', 'bookings', 'reviews', 'location', 'owner', 'galleryImages', 'hubSocialAccounts');
        return $this->successResponse(new HubResource($hub), 'Hub created successfully', 201);
    }

    public function show($slug)
    {
        $hub = Hub::with('location', 'owner', 'offers', 'bookings', 'reviews', 'images')
            ->where('slug', $slug)
            ->first();

        if (!$hub) {
            return $this->errorResponse('Hub not found', 404);
        }

        return $this->successResponse(new HubResource($hub), 'Hub retrieved successfully');
    }
    public function update(HubRequest $request, $slug)
    {
        // dd($request->all());
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
            if ($request->has('service_ids')) {
                $hub->services()->sync($request->input('service_ids'));
            }

            $hub->update($request->validated());

            // تحديث الصورة الرئيسية
            if ($request->hasFile('main_image')) {
                ImageHelper::updateImage($hub, $request->file('main_image'), 'hubs/main', 'main', 'custom');
            }
            // تحديث معرض الصور
            // تحديث معرض الصور
            if ($request->has('delete_gallery_ids') || $request->hasFile('gallery')) {
                $hub->updateGallery(
                    $request->file('gallery', []),
                    $request->input('delete_gallery_ids', [])
                );
            }
            // dd($request->hasFile('gallery'));
            // تحديث الحسابات الاجتماعية
            if ($request->has('social_accounts')) {
                $hub->hubSocialAccounts()->delete();
                $hub->hubSocialAccounts()->createMany($request->input('social_accounts', []));
            }

            DB::commit();

            $hub->load('images', 'services', 'location', 'owner');
            // dd($hub->load('images'));

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
