<?php

namespace Modules\Subscription\Policies;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Subscription\Models\SubscriptionPlan;

class SubscriptionPlanPolicy {
    use HandlesAuthorization;

    /**
     * Summary of before
     * @param User $user
     * @return bool|null
     */
    public  function before(User $user) {
        if ($user->hasRole(UserRoles::Admin->value)) {
            return true;
        }
        return null;
    }

    /**
     * Summary of viewAny
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user) {
        return $user->hasRole(UserRoles::Instructor->value);
    }
    /**
     * Summary of view
     * @param User $user
     * @param SubscriptionPlan $plan
     * @return bool
     */
    public  function view(User $user, SubscriptionPlan $plan) {
        return $user->hasRole(UserRoles::Instructor->value);
    }
    /**
     * Summary of create
     * @param User $user
     * @return bool
     */
    public function create(User $user){
        return false;
    }
    /**
     * Summary of update
     * @param User $user
     * @param SubscriptionPlan $plan
     * @return bool
     */
    public function update(User $user,SubscriptionPlan $plan){
        return false;
    }
    /**
     * Summary of delete
     * @param User $user
     * @param SubscriptionPlan $plan
     * @return bool
     */
    public  function delete(User $user,SubscriptionPlan $plan){
       return false;
    }
}
