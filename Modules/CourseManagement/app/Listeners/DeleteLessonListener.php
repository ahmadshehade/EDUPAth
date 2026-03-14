<?php

namespace Modules\CourseManagement\Listeners;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\CourseManagement\Events\DeleteLessonEvent;
use Modules\CourseManagement\Notifications\DeleteLessonNotification;

class DeleteLessonListener implements ShouldQueue
{
    /**
  
    /**
     * Handle the event.
     */
    public function handle(DeleteLessonEvent $event): void {
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', UserRoles::Admin->value);
        })->get();
        $teacher=User::where('id',$event->data['teacherId'])->first();
       $users= $users->push($teacher);
        $students=User::whereIn('id',$event->data['studentsId'])->get();
        $users=$users->merge($students);
        Notification::send($users->unique('id'),new DeleteLessonNotification($event->data,$event->user_id));

    }
}
