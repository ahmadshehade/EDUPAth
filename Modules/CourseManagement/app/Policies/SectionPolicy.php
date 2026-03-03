<?php

namespace Modules\CourseManagement\Policies;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\CourseManagement\Models\Section;

class SectionPolicy {
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
    public function viewAny(User $user) {
        return $user->hasAnyRole([UserRoles::Instructor->value, UserRoles::Student->value]);
    }

    /**
     * Summary of view
     * @param User $user
     * @return bool
     */
    public function view(User $user, Section $section) {
        if ($user->hasRole(UserRoles::Student->value)&&$section->course->is_published===true) {
            return true;
        } elseif (
            $user->hasRole(UserRoles::Instructor->value) &&
            $user->id === $section->course->instructor->id
        ) {
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
     * @param Section $section
     * @return bool
     */
    public function update(User $user, Section $section) {
        return $user->hasRole(UserRoles::Instructor->value)
            && $user->id === $section->course->instructor->id;
    }

    /**
     * Summary of delete
     * @param User $user
     * @param Section $section
     * @return bool
     */
    public function delete(User $user, Section $section) {
        return $user->hasRole(UserRoles::Instructor->value)
            && $user->id === $section->course->instructor->id;
    }
}
