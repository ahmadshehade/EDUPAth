<?php

namespace Modules\CourseManagement\Listeners;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\CourseManagement\Events\CreateSectionEvent;
use Modules\CourseManagement\Notifications\CreateSectionNotification;

class CreateSectionlistener  implements ShouldQueue
{
   
    /**
     * Handle the event.
     */
    public function handle(CreateSectionEvent $event): void {
        $users=User::whereHas('roles',function($query){
            $query->where('name',UserRoles::Admin->value);
        })->get();

        Notification::send($users,new CreateSectionNotification($event->section,$event->user_id));
    }
}
