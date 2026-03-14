<?php

namespace Modules\CourseManagement\Policies;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\CourseManagement\Models\Course;

class CoursePolicy {
    use HandlesAuthorization;

    /**
     * Summary of before
     * @param User $user
     * @return bool|null
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
            UserRoles::Admin->value,
            UserRoles::Instructor->value,
            UserRoles::Student->value
        ]);
    }

    /**
     * Summary of view
     * @param User $user
     * @param Course $course
     * @return bool
     */
    public  function view(User $user, Course $course) {
        return $user->hasAnyRole([
            UserRoles::Admin->value,
            UserRoles::Instructor->value,
            UserRoles::Student->value
        ]);
    }

    /**
     * Summary of create
     * @param User $user
     * @return string
     */
    public function create(User $user) {
        return UserRoles::Instructor->value;
    }

    /**
     * Summary of update
     * @param User $user
     * @param Course $course
     * @return bool
     */
    public  function update(User $user, Course $course) {
        return UserRoles::Instructor->value && $course->instructor_id === $user->id;
    }

    /**
     * Summary of delete
     * @param User $user
     * @param Course $course
     * @return bool
     */
    public function delete(User $user, Course $course) {
        return UserRoles::Instructor->value && $course->instructor_id === $user->id;
    }
}
