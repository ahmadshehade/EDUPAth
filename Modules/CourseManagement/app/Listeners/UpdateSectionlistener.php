<?php

namespace Modules\CourseManagement\Listeners;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\CourseManagement\Events\UpdateSectionEvent;
use Modules\CourseManagement\Notifications\UpdateSectionNotification;

class UpdateSectionlistener implements ShouldQueue {


    /**
     * Handle the event.
     */
    public function handle(UpdateSectionEvent $event): void {

        $users = User::whereHas('roles', function ($query) {
            $query->where('name', UserRoles::Admin->value);
        })->get();
       $teacher = $event->section->course->instructor;
        $users = $users->push($teacher);
        
        if ($event->section->is_published === true) {
          $students = $event->section->course->students;
          $users=$users->merge($students);
        } 

        Notification::send($users, new UpdateSectionNotification($event->user_id, $event->section));
    }
}
