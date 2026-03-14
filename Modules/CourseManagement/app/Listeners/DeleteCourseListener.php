<?php

namespace Modules\CourseManagement\Listeners;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\CourseManagement\Events\DeleteCourseEvent;
use Modules\CourseManagement\Notifications\DeleteCourseNotification;

class DeleteCourseListener implements ShouldQueue {


    /**
     * Handle the event.
     */
    public function handle(DeleteCourseEvent $event): void {

        $users = User::whereHas('roles', function ($query) {
            $query->where('name', UserRoles::Admin->value);
        })->get();

        $teacherId = $event->data['teacherId'];
        $teacher = User::where('id', $teacherId)->first();
        if ($teacher) {
           $users= $users->push($teacher);
        }
        $students = User::whereIn('id',$event->data['students'])->get();
        $users=$users->merge($students);
        Notification::send($users, new DeleteCourseNotification($event->user_id, $event->data));
    }
}
