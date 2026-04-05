<?php

namespace Modules\Subscription\Listeners;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\Subscription\Events\UpdateSubscriptionEvent;
use Modules\Subscription\Notifications\UpdateSubscriptionNotification;

class UpdateSubscriptionListener  implements ShouldQueue
{


    /**
     * Handle the event.
     */
    public function handle(UpdateSubscriptionEvent $event): void
    {
        $admins = User::whereHas('roles', function ($query) use ($event) {
            $query->where('name', UserRoles::Admin->value);
        })->get();

        $student=$event->subscription->user;
        $admins->push($student);
        Notification::send($admins,new UpdateSubscriptionNotification($event->userId,$event->subscription));
    }
}
