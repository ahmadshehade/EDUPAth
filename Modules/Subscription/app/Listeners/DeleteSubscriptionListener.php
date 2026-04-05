<?php

namespace Modules\Subscription\Listeners;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\Subscription\Events\DeleteSubscriptionEvent;
use Modules\Subscription\Notifications\DeleteSubscriptionNotification;

class DeleteSubscriptionListener implements ShouldQueue
{

    /**
     * Handle the event.
     */
    public function handle(DeleteSubscriptionEvent $event): void
    {
        $admins = User::whereHas('roles', function ($query) use ($event) {
            $query->where('name', UserRoles::Admin->value);
        })->get();
        $student = User::where('id', $event->data['user_id'])->first();
        if ($student) {
            $admins->push($student);
        }
        Notification::send($admins, new DeleteSubscriptionNotification($event->userId, $event->data));
    }
}
