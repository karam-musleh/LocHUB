<?php

namespace App\Http\Controllers\Api\Hubs;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialRequest;
use App\Http\Resources\SocialResource;
use App\Models\Hub;
use App\Models\SocialAccount;
use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SocialController extends Controller
{
    //
    use ApiResponseTrait , AuthorizesRequests;
    /**
     * @group Hubs / Social Accounts
     *
     * Retrieve all social accounts for a hub.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "platform": "facebook",    
     *       "url": "https://facebook.com/hub"
     *     }
     *   ]
     * }
     */
    public function index(Hub $hub)
    {
        $socialAccounts = $hub->socialAccounts()->get();

        return $this->successResponse(
            SocialResource::collection($socialAccounts),
            'Social accounts retrieved successfully'
        );
    }
    public function store(Hub $hub, SocialRequest $request)
    {

        $this->authorize('create', [SocialAccount::class, $hub]);
        $socialAccount = $hub->socialAccounts()->create($request->only('platform', 'url'));

        return $this->successResponse(
            new SocialResource($socialAccount),
            'Social account created successfully',
            201
        );
    }
    public function show(Hub $hub, SocialAccount $socialAccount)
    {
        return $this->successResponse(
            new SocialResource($socialAccount),
            'Social account retrieved successfully'
        );
    }
    public function update(Hub $hub, SocialAccount $socialAccount, SocialRequest $request)
    {
        $this->authorize('update', $socialAccount);

        $socialAccount->update($request->only('platform', 'url'));

        return $this->successResponse(
            new SocialResource($socialAccount),
            'Social account updated successfully'
        );
    }
    public function destroy(Hub $hub, SocialAccount $socialAccount)
    {
        $this->authorize('delete', $socialAccount);
        $socialAccount->delete();
        return $this->successResponse(
            null,
            'Social account deleted successfully'
        );
    }
}
