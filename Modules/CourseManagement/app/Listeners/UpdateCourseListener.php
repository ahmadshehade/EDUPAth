<?php

namespace Modules\CourseManagement\Listeners;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\CourseManagement\Events\UpdateCourseEvent;
use Modules\CourseManagement\Notifications\CourseUpdateNotification;

class UpdateCourseListener implements ShouldQueue {


    /**
     * Handle the event.
     */
    public function handle(UpdateCourseEvent $event): void {
        if ($event->course->is_published === true) {

            $users = User::whereHas('roles', function ($q) {
                $q->whereIn('name', [
                    UserRoles::Instructor->value,
                    UserRoles::Student->value,
                    UserRoles::Admin->value,
                ]);
            })->get();

            Notification::send(
                $users,
                new CourseUpdateNotification($event->course, $event->user_id)
            );
        } else {
            $admins = User::whereHas('roles', function ($q) {
                $q->where('name', UserRoles::Admin->value);
            })->get();
            $instructor = $event->course->instructor;
            $users = $admins->push($instructor);
            Notification::send(
                $users,
                new CourseUpdateNotification($event->course, $event->user_id)
            );
        }
    }
}
