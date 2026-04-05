<?php

namespace Modules\Subscription\Listeners;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\Subscription\Events\DeleteSubscriptionPlanEvent;
use Modules\Subscription\Notifications\DeleteSubscriotionPlanNotification;

class DeleteSubscriptionPlanListener implements ShouldQueue
{

    /**
     * Handle the event.
     */
    public function handle(DeleteSubscriptionPlanEvent $event): void
    {
        $users = User::whereHas('roles', function ($query) use ($event) {
            $query->whereIn('name', [UserRoles::Instructor->value]);
        })->get();
    
        $all = $users->merge($event->data['students']);
        Notification::send($all, new DeleteSubscriotionPlanNotification($event->user_id, $event->data));
    }
}
