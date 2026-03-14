<?php

namespace Modules\CourseManagement\Listeners;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\CourseManagement\Events\UpdateLessonEvent;
use Modules\CourseManagement\Notifications\UpdateLessonNotification;

class UpdateLessonListener implements ShouldQueue {


    /**
     * Handle the event.
     */
    public function handle(UpdateLessonEvent $event): void {

        $users = User::whereHas('roles', function ($query) {
            $query->where('name', UserRoles::Admin->value);
        })->get();
        $teacher = $event->lesson->section->course->instructor;
        $group = $users->push($teacher);
        $students=$event->lesson->section->course->students;
        $group = $group->merge($students);
        Notification::send($group, new UpdateLessonNotification($event->user_id, $event->lesson));
    }
}
