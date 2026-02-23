<?php

namespace App\Policies;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class UserPolicy {

    /**
     * Summary of before
     * @param User $user
     * 
     */
    public function  before(User $user) {
        if ($user->hasRole(UserRoles::Admin->value)) {
            return true;
        }
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $targetUser): bool {
        return $user->id === $targetUser->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user,User $targetUser): bool {
        return $user->id === $targetUser->id;;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user,User $targetUser): bool {
           return $user->id === $targetUser->id;;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool {
        return false;
    }
}
