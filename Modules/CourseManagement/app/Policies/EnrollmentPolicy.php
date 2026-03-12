<?php

namespace Modules\CourseManagement\Policies;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\CourseManagement\Models\Enrollment;

class EnrollmentPolicy {
    use HandlesAuthorization;

    /**
     * Summary of before
     * @param User $user
     * 
     */
    public  function before(User  $user) {
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
        return $user->hasAnyRole([
            UserRoles::Admin->value,
            UserRoles::Instructor->value,
            UserRoles::Student->value
        ]);
    }

    /**
     * Summary of view
     * @param User $user
     * @param Enrollment $enrollment
     * @return bool
     */
    public function  view(User $user, Enrollment $enrollment) {
        return ($user->hasRole(UserRoles::Instructor->value) && $enrollment->course->instructor_id === $user->id) ||
            ($user->hasRole(UserRoles::Student->value) && $user->id === $enrollment->user_id);
    }

    /**
     * Summary of create
     * @param User $user
     * @return bool
     */
    public function create(User $user){
        return $user->hasRole(UserRoles::Student->value);
    }
    /**
     * Summary of update
     * @param User $user
     * @param Enrollment $enrollment
     * @return bool
     */
    public function update(User $user ,Enrollment $enrollment){
        return $user->hasRole(UserRoles::Student->value)&&$user->id===$enrollment->user_id&&$enrollment->progress ===0 ;
    }

    /**
     * Summary of delete
     * @param User $user
     * @param Enrollment $enrollment
     * @return bool
     */
    public function delete(User $user,Enrollment $enrollment){
        return $user->hasRole(UserRoles::Student->value)&&$user->id===$enrollment->user_id&&$enrollment->progress ===0 ;
    }
}
