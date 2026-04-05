<?php

namespace Modules\Subscription\Listeners;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\Subscription\Events\UpdateSubscriptionPlanEvent;
use Modules\Subscription\Notifications\UpdateSubscriptionPlanNotification;

class UpdateSubscriptionPlanListener implements ShouldQueue
{


    /**
     * Handle the event.
     */
    public function handle(UpdateSubscriptionPlanEvent $event): void
    {
        User::whereHas('roles', function ($query) use ($event) {
            $query->whereIn('name', [UserRoles::Student->value, UserRoles::Instructor->value]);
        })->chunk(100,function($users)use($event){
            Notification::send($users,new UpdateSubscriptionPlanNotification($event->userId,$event->subscriptionPlan));
        });
    }
}
