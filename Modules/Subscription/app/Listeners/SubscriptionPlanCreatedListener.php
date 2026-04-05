<?php

namespace Modules\Subscription\Listeners;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\Subscription\Events\SubscriptionPlanCreatedEvent;
use Modules\Subscription\Notifications\SubscriptionPlanCreateNotification;

class SubscriptionPlanCreatedListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(SubscriptionPlanCreatedEvent $event): void
    {
        User::whereHas('roles', function ($q) {
            $q->whereIn('name', [UserRoles::Student->value, UserRoles::Instructor->value]);
        })->chunk(100, function ($users) use ($event) {
            Notification::send($users, new SubscriptionPlanCreateNotification($event->user_id, $event->subscriptionPlan));
        });
    }
}