<?php

namespace Modules\Payments\Listeners;

use App\Enums\UserRoles;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Payments\Events\SoftDeleteInvoiceEvent;
use Modules\Payments\Notifications\SoftDeleteInvoiceNotification;

class SoftDeleteInvoiceListener implements ShouldQueue
{
    public function handle(SoftDeleteInvoiceEvent $event): void
    {
        try {
            $adminUsers = User::whereHas('roles', function ($query) {
                $query->where('name', UserRoles::Admin->value);
            })->get();
            $owner = User::find($event->owner_id);
            $recipients = $adminUsers;
            if ($owner) {
                $recipients = $recipients->push($owner);
            }
            $recipients = $recipients->unique('id')->values();
            Notification::send(
                $recipients,
                new SoftDeleteInvoiceNotification(
                    $event->actor_id,
                    $event->invoices->toArray(),
                )
            );
        } catch (Exception   $e) {
            Log::error('Fail To SoftDelete Invoice :' . $e->getMessage());
            throw $e;
        }
    }
}
