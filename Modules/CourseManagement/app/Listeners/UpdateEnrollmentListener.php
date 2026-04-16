<?php

namespace Modules\CourseManagement\Listeners;

use App\Enums\UserRoles;
use App\Models\User;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\CourseManagement\Events\UpdateEnrollmentEvent;
use Modules\CourseManagement\Notifications\UpdateEnrollmentNotification;

class UpdateEnrollmentListener implements ShouldQueue
{


    /**
     * Handle the event.
     */
    public function handle(UpdateEnrollmentEvent $event): void
    {
        $adminUsers = User::whereHas('roles', function ($query) {
            $query->where('name', UserRoles::Admin);
        })->get();
        $student = $event->enrollment->user;
        if ($student) {
            $adminUsers = $adminUsers->push($student)->unique('id');
        }
        Notification::send($adminUsers, new UpdateEnrollmentNotification($event->user_id, $event->enrollment));
    }
}
