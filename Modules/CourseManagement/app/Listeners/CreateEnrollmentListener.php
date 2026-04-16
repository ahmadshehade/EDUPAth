<?php

namespace Modules\CourseManagement\Listeners;

use App\Enums\UserRoles;
use App\Models\User;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\CourseManagement\Events\CreateEnrollmentEvent;
use Modules\CourseManagement\Notifications\CreateEnrollmentNotification;

class CreateEnrollmentListener implements ShouldQueue
{


    /**
     * Handle the event.
     */
    public function handle(CreateEnrollmentEvent $event): void
    {
        $adminUsers = User::whereHas('roles', function ($query) {
            $query->where('name', UserRoles::Admin);
        })->get();
        $student = $event->enrollment->user;
        if ($student) {
            $adminUsers->push($student)->unique('id');
        }
        Notification::send($adminUsers, new CreateEnrollmentNotification($event->user_id, $event->enrollment));
    }
}
