<?php

namespace Modules\Payments\Listeners;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\Payments\Events\CreateInvoiceForSubscriptionEvent;
use Modules\Payments\Notifications\CreateInvovoiceForSubscriptionNotification;

class CreateInvoiceForSubscriptionListener implements ShouldQueue
{

    /**
     * Summary of handle
     * @param CreateInvoiceForSubscriptionEvent $event
     * @return void
     */
    public function handle(CreateInvoiceForSubscriptionEvent $event): void
    {
        $adminsUsers = User::whereHas('roles', function ($query) {
            $query->where('name', UserRoles::Admin->value);
        })->get();
        $user = $event->invoice->user;
        $adminsUsers->push($user)->unique();
        Notification::send($adminsUsers, new CreateInvovoiceForSubscriptionNotification($event->invoice));
    }
}
