<?php

namespace App\Notifications;

use App\Models\Hub;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
// use Illuminate\Notifications\Messages\BroadcastMessage;
class HubCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $hub;

    public function __construct(Hub $hub)
    {
        $this->hub = $hub;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //         ->line('The introduction to the notification.')
    //         ->action('Notification Action', url('/'))
    //         ->line('Thank you for using our application!');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'hub_id' => $this->hub->id,
            'hub_name' => $this->hub->name,
            'owner_name' => $this->hub->owner->name,
            'message' => "تم طلب إنشاء Hub جديد: {$this->hub->name}",
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'hub_id' => $this->hub->id,
            'hub_name' => $this->hub->name,
            'owner_name' => $this->hub->owner->name,
            'message' => "تم طلب إنشاء Hub جديد: {$this->hub->name}",
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function broadcastChannel(): string
    {
        return 'admin-notifications';
    }
}
