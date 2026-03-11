<?php

namespace Modules\CourseManagement\Policies;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\CourseManagement\Models\Lesson;

class LessonPolicy {
    use HandlesAuthorization;

    /**
     * Summary of before
     * @param User $user
     * 
     */
    public function before(User $user) {
        if ($user->hasRole(UserRoles::Admin->value)) {
            return true;
        }
    }


    /**
     * Summary of viewAny
     * @param User $user
     * @return bool
     */
    public  function viewAny(User $user) {
        return $user->hasAnyRole([
            UserRoles::Instructor->value,
            UserRoles::Student->value,
        ]);
    }

    /**
     * Summary of view
     * @param User $user
     * @param Lesson $lesson
     * @return bool
     */
    public  function view(User $user, Lesson $lesson) {
        if ($user->hasRole(UserRoles::Instructor->value) && ($user->id === $lesson->section->course->instructor->id)) {
            return true;
        } elseif ($user->hasRole(UserRoles::Student->value) && $lesson->section->is_published) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Summary of create
     * @param User $user
     * @return bool
     */
    public function create(User $user) {
        return $user->hasRole(UserRoles::Instructor->value);
    }

    /**
     * Summary of update
     * @param User $user
     * @param Lesson $lesson
     * @return bool
     */
    public function update(User $user, Lesson $lesson) {
        return $user->hasRole(UserRoles::Instructor->value) &&
            $user->id === $lesson->section->course->instructor->id;
    }

    /**
     * Summary of delete
     * @param User $user
     * @param Lesson $lesson
     * @return bool
     */
    public function delete(User $user,Lesson $lesson){
             return $user->hasRole(UserRoles::Instructor->value) &&
            $user->id === $lesson->section->course->instructor->id;
    }
}
