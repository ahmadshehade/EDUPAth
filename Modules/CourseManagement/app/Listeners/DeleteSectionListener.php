<?php

namespace Modules\CourseManagement\Listeners;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\CourseManagement\Events\DeleteSectionEvent;
use Modules\CourseManagement\Notifications\DeleteSectionNotification;

class DeleteSectionListener {


    /**
     * Handle the event.
     */
    public function handle(DeleteSectionEvent $event): void {

        $users = User::whereHas('roles', function ($query) {
            $query->where('name', UserRoles::Admin->value);
        })->get();
        $teacher = User::where('id', $event->data['teacher_id'])->first();
        $users->push($teacher);
        $students = User::whereIn('id', $event->data['student_ids'])->get();
        if ($students) {
            $users = $users->concat($students);
        }
        Notification::send($users->unique('id'), new DeleteSectionNotification($event->data, $event->user_id));
    }
}
