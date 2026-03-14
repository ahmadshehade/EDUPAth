<?php

namespace Modules\CourseManagement\Listeners;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\CourseManagement\Events\CreateLessonEvent;
use Modules\CourseManagement\Notifications\CreateLessonNotification;

class CreateLessonListener implements ShouldQueue
{
   
    /**
     * Handle the event.
     */
    public function handle(CreateLessonEvent$event): void {
        $users=User::whereHas('roles',function($query){
            $query->where('name',UserRoles::Admin->value);
        })->get();
        $teacher = $event->lesson->section->course->instructor;
        $group=$users->push($teacher);
        $students=$event->lesson->section->course->students;
        $group=$group->merge($students);
        Notification::send($group,new CreateLessonNotification($event->lesson,$event->user_id));
    }
}
