<?php

namespace App\Http\Controllers\Api\Hubs;

use App\Actions\Hub\CreateHubAction;
use App\Enum\HubStatus;
use App\Enum\UserRole;
// use App\Events\HubCreated;
use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\HubRequest;
use App\Http\Resources\HubResource;
use App\Mail\HubApprovedMail;
use App\Mail\HubRejectedMail;
use App\Models\Hub;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HubController extends Controller
{
    use ApiResponseTrait;
    //
    private function getUserHub($slug)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }
        return Hub::where('slug', $slug)
            ->when($user->role !== UserRole::ADMIN, function ($q) use ($user) {
                $q->where('owner_id', $user->id);
            })
            ->first();
    }
    public function myHubs()
    {
        $user = Auth::guard('api')->user();

        $hubs = Hub::with('location.parent', 'owner', 'images')
            ->where('owner_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->successResponse(HubResource::collection($hubs), __('messages.my_hubs_retrieved'));
    }

    public function store(HubRequest $request, CreateHubAction $action)
    {

        $hub = $action->execute(
            $request->validated(),
            Auth::id()
        );
        return $this->successResponse(new HubResource($hub), __('messages.hub_created'), 201);
    }

    public function show($slug)
    {
        $hub = $this->getUserHub($slug);
        if (!$hub) {
            return $this->errorResponse(__('messages.hub_not_found'), 404);
        }

        $hub->load('location.parent', 'owner', 'offers', 'reviews', 'images', 'services', 'customServices', 'hubSocialAccounts');

        return $this->successResponse(new HubResource($hub), __('messages.hub_retrieved'));
    }
    public function update(HubRequest $request, $slug)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();

            $user = Auth::guard('api')->user();
            $hub = $this->getUserHub($slug);

            if (!$hub) {
                return $this->errorResponse(__('messages.hub_not_found'), 404);
            }

            $isOwner = $hub->owner_id === $user->id;

            if (!$isOwner && !$user->isAdmin()) {
                return $this->errorResponse(__('messages.not_authorized_to_update_hub'), 403);
            }
            if ($request->has('add_service_ids')) {
                $hub->services()->attach($request->input('add_service_ids'));
            }

            if ($request->has('remove_service_ids')) {
                $hub->services()->detach($request->input('remove_service_ids'));
            }

            $hub->update($request->validated());
            // تحديث الصورة الرئيسية
            if ($request->hasFile('main_image')) {
                ImageHelper::updateImage($hub, $request->file('main_image'), 'hubs/main', 'main', 'custom');
            }
            // تحديث معرض الصور
            // تحديث معرض الصور

            // تحديث الحسابات الاجتماعية
            if ($request->has('social_accounts')) {
                $hub->hubSocialAccounts()->delete();
                $hub->hubSocialAccounts()->createMany($request->input('social_accounts', []));
            }

            DB::commit();
            if ($request->has('delete_gallery_ids') || $request->hasFile('gallery')) {
                $hub->updateGallery(
                    $request->file('gallery', []),
                    $request->input('delete_gallery_ids', [])
                );
            }
            // dd($hub->images);
            $hub->load('location.parent', 'owner', 'offers', 'bookings', 'reviews', 'images', 'services', 'customServices', 'hubSocialAccounts');
            // dd();
            return $this->successResponse(new HubResource($hub), __('messages.hub_updated'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(
                __('messages.hub_update_failed'),
                500,
                ['error' => $e->getMessage()]
            );
        }
    }



    public function destroy($slug)
    {
        $user = Auth::guard('api')->user();

        $hub = $this->getUserHub($slug);


        if (!$hub) {
            return $this->errorResponse(__('messages.hub_not_found'), 404);
        }

        $isOwner = $hub->owner_id === $user->id;

        if (!$isOwner && !$user->isAdmin()) {
            return $this->errorResponse(__('messages.not_authorized_to_update_hub'), 403);
        }

        // حذف كل الصور المرتبطة بالهب
        ImageHelper::deleteAll($hub);


        // حذف الهب نفسه
        $hub->delete();

        return $this->successResponse(null, __('messages.hub_deleted'));
    }



    // public function changeStatus(Request $request, Hub $hub)
    // {
    //     $request->validate([
    //         'status' => ['required', 'in:' . implode(',', array_map(fn($status) => $status->value, HubStatus::cases()))],
    //         'rejection_reason' => ['nullable', 'string', 'required_if:status,' . HubStatus::REJECTED],
    //     ]);

    //     if (!$hub) {
    //         return $this->errorResponse('Hub not found', 404);
    //     }

    //     $oldStatus = $hub->status;
    //     $newStatus = $request->status;

    //     // تحديث الـ Hub
    //     $hub->status = $newStatus;
    //     $hub->rejection_reason = $newStatus === HubStatus::REJECTED->value ? $request->rejection_reason : null;
    //     $hub->save();

    //     // ⭐️ إرسال الإيميل بناءً على الـ Status
    //     $this->sendStatusEmail($hub, $newStatus, $request->rejection_reason ?? null);

    //     return $this->successResponse(
    //         new HubResource($hub),
    //         "Hub status changed to {$hub->status}"
    //     );
    // }
    public function changeStatus(Request $request, Hub $hub)
    {
        $request->validate([
            'status' => [
                'required',
                'in:' . implode(',', array_map(fn($status) => $status->value, HubStatus::cases()))
            ],
            'rejection_reason' => [
                'nullable',
                'string',
                'required_if:status,' . HubStatus::REJECTED->value
            ],
        ]);

        // نحول من string إلى Enum
        $newStatus = HubStatus::from($request->status);

        // تحديث
        $hub->status = $newStatus;

        $hub->rejection_reason = $newStatus === HubStatus::REJECTED
            ? $request->rejection_reason
            : null;

        $hub->save();

        // إرسال الإيميل (نرسل value مش object)
        $this->sendStatusEmail(
            $hub,
            $newStatus->value,
            $request->rejection_reason
        );

        return $this->successResponse(
            new HubResource($hub),
            "Hub status changed to " . $hub->status->value
        );
    }
    private function sendStatusEmail($hub, $status, $rejectionReason = null)
    {
        try {
            if ($status === HubStatus::APPROVED->value) {
                Mail::to($hub->owner->email)
                    ->queue(new HubApprovedMail($hub));
            } elseif ($status === HubStatus::REJECTED->value) {
                Mail::to($hub->owner->email)
                    ->queue(new HubRejectedMail($hub, $rejectionReason));
            }
        } catch (\Exception $e) {
            \Log::error(__('messages.failed_to_send_hub_status_email') . ' ' . $e->getMessage());
        }
    }

    public function updateFeatured(Request $request, Hub $hub)
{
    $validated = $request->validate([
        'is_featured' => ['required', 'boolean'],

        'featured_from' => [
            'nullable',
            'date',
        ],

        'featured_until' => [
            'nullable',
            'date',
            'after_or_equal:featured_from',
        ],

        'featured_priority' => [
            'nullable',
            'integer',
            'min:0',
        ],
    ]);

    // ما نسمح بتمييز Hub غير معتمد
    if ($validated['is_featured'] && !$hub->isApproved()) {
        return $this->errorResponse(
            __('messages.only_approved_hubs_can_be_featured'),
            422
        );
    }

    $hub->update([
        'is_featured' => $validated['is_featured'],

        'featured_from' => $validated['is_featured']
            ? ($validated['featured_from'] ?? now())
            : null,

        'featured_until' => $validated['is_featured']
            ? ($validated['featured_until'] ?? null)
            : null,

        'featured_priority' => $validated['is_featured']
            ? ($validated['featured_priority'] ?? 0)
            : 0,
    ]);

    return $this->successResponse([
        'hub' => new HubResource($hub->fresh()),
    ], __('messages.hub_featured_status_updated'));
}
}
