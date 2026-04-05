<?php

namespace App\Providers;

use App\Events\HubCreated;
use App\Listeners\NotifyAdminHubCreated;
use App\Models\Hub;
use App\Models\Service;
use App\Models\SocialAccount;
use App\Policies\HubPolicy;
use App\Policies\ServicePolicy;
use App\Policies\SocialAccountPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    // protected $policies = [
    //     Service::class => ServicePolicy::class,
    // ];


    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Service::class, ServicePolicy::class);
        Gate::policy(Hub::class, HubPolicy::class);
        Gate::policy(SocialAccount::class, SocialAccountPolicy::class);
        Event::listen(HubCreated::class, NotifyAdminHubCreated::class);

        //
        // Gate::policy(Service::class, ServicePolicy::class);
        // Gate::policy(Hub::class, HubPolicy::class);
    }
}
