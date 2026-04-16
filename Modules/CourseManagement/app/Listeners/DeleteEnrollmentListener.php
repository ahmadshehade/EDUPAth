<?php

namespace Modules\CourseManagement\Listeners;

use App\Enums\UserRoles;
use App\Models\User;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\CourseManagement\Events\DeleteEnrollmentEvent;
use Modules\CourseManagement\Notifications\DeleteEnrollmentNotification;

class DeleteEnrollmentListener implements ShouldQueue
{

    /**
     * Handle the event.
     */
    public function handle(DeleteEnrollmentEvent $event): void
    {
        $adminUsers = User::whereHas('roles', function ($query) {
            $query->where('name', UserRoles::Admin);
        })->get();
        $student = $event->data['user'];
        if ($student) {
            $adminUsers->push($student)->unique('id');
        }
        Notification::send($adminUsers, new DeleteEnrollmentNotification($event->user_id, $event->data));
    }
}
