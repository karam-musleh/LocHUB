<?php

namespace App\Listeners;

use App\Enum\UserRole;
use App\Events\HubCreated;
use App\Models\User;
use App\Notifications\HubCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyAdminHubCreated
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(HubCreated $event): void
    {
        // احصل على جميع الـ Admins
        $admins = User::where('role', UserRole::ADMIN->value)->get();

        // أرسل الإشعار
        Notification::send($admins, new HubCreatedNotification($event->hub));
    }
}
