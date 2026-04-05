<?php

namespace Modules\Subscription\Listeners;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\Subscription\Events\CreateSubscriptionEvent;
use Modules\Subscription\Notifications\CreateSubscriptionNotification;

class CreateSubsciptionListener implements ShouldQueue
{

    /**
     * Handle the event.
     */
    public function handle(CreateSubscriptionEvent $event): void
    {
        $admins = User::whereHas('roles', function ($query) use ($event) {
            $query->where('name', UserRoles::Admin->value);
        })->get();
        $student = User::where('id', $event->subscription->user_id)->first();
        if($student){
            $admins->push($student);
        }
        Notification::send($admins, new CreateSubscriptionNotification($event->user_id, $event->subscription));
    }
}
