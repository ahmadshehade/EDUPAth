<?php

namespace Modules\Payments\Policies;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Payments\Models\Invoice;

class InvoicePolicy
{
    use HandlesAuthorization;

    /**
     * Summary of before
     * @param User $user
     * @return bool|null
     */
    public function before(User $user)
    {
        if ($user->hasRole(UserRoles::Admin->value)) {
            return true;
        } else {
            return null;
        }
    }

    /**
     * Summary of viewAny
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->hasRole(UserRoles::Student->value);
    }

    /**
     * Summary of view
     * @param User $user
     * @param Invoice $invoice
     * @return bool
     */
    public function view(User $user, Invoice $invoice)
    {
        return $user->hasRole(UserRoles::Student->value) && $user->id === $invoice->user_id;
    }
}
