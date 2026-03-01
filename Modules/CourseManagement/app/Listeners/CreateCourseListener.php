<?php

namespace Modules\CourseManagement\Listeners;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\CourseManagement\Events\CreateCourseEvent;
use Modules\CourseManagement\Notifications\CourseCreateNotification;

class CreateCourseListener implements ShouldQueue 
{
    public function handle(CreateCourseEvent $event): void
    {
       
        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', [
                UserRoles::Instructor->value,
                UserRoles::Student->value,
                UserRoles::Admin->value
            ]);
        })->get();
  

        
        Notification::send($users, new CourseCreateNotification($event->course, $event->user_id));
    }
}