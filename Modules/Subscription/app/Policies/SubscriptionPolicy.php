<?php

namespace Modules\Subscription\Policies;

use App\Enums\SubscriptionStatus;
use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Subscription\Models\Subscription;

class SubscriptionPolicy
{


    use HandlesAuthorization;

    /**
     * Summary of before
     * @param User $user
     * @return bool|null
     */
    public  function before(User $user)
    {
        if ($user->hasRole(UserRoles::Admin->value)) {
            return true;
        }
        return null;
    }

    /**
     * Summary of viewAny
     * @param User $user
     * @return void
     */
    public function viewAny(User $user)
    {
        $user->hasAnyRole([
            UserRoles::Student->value
        ]);
    }

    /**
     * Summary of view
     * @param User $user
     * @param Subscription $subscription
     * @return bool
     */
    public function  view(User $user, Subscription $subscription)
    {
        return $user->hasRole(UserRoles::Student->value) &&
            $user->id === $subscription->user_id;
    }

    /**
     * Summary of create
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasRole(UserRoles::Student->value);
    }

    /**
     * Summary of update
     * @param User $user
     * @param Subscription $subscription
     * @return bool
     */
    public function update(User $user, Subscription $subscription)
    {
        return $user->hasRole(UserRoles::Student->value) &&
            ($user->id === $subscription->user_id)
            && ($subscription->status = SubscriptionStatus::Pending->value);
    }

    /**
     * Summary of delete
     * @param User $user
     * @param Subscription $subscription
     * @return bool
     */
    public  function delete(User $user, Subscription $subscription)
    {
        return $user->hasRole(UserRoles::Student->value) &&
            ($user->id === $subscription->user_id) &&
            ($subscription->status = SubscriptionStatus::Pending->value);
    }
}
