<?php

namespace Modules\Payments\Listeners;

use App\Enums\UserRoles;
use App\Models\User;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\Payments\Events\CreateinvoiceForInrollmentEvent;
use Modules\Payments\Notifications\CreateInvoiceForInrollmentNotification;

class CreateInvoiceForEnrollmentListener implements ShouldQueue
{


    /**
     * Handle the event.
     */
    public function handle(CreateinvoiceForInrollmentEvent $event): void
    {
        $adminsUsers = User::whereHas('roles', function ($query) {
            $query->where('name', UserRoles::Admin->value);
        })->get();
        $user = $event->invoice->user;
        $adminsUsers->push($user);
        Notification::send($adminsUsers, new CreateInvoiceForInrollmentNotification($event->user_id, $event->invoice));
    }
}
