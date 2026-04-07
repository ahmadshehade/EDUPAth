<?php

namespace Modules\Payments\Services;

use App\Enums\NameOfCache;
use App\Enums\UserRoles;
use App\Models\User;
use App\Traits\FilterableServiceTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Modules\Payments\Models\PaymentMethod;
use Modules\Payments\Notifications\CreatePaymentMethodNotification;
use Modules\Payments\Notifications\DeletePaymentMethodNotification;
use Modules\Payments\Notifications\UpdatePaymentMethodNotification;

class PaymentMethodServices
{
    use FilterableServiceTrait;

    /**
     * Summary of genKey
     * @param mixed $filters
     * @return string
     */
    public function genKey($filters)
    {
        $user = Auth::user();
        ksort($filters);
        $userkey = $user ? $user->id . '_' . md5(json_encode($user->roles->pluck('id')->sort())): 'guest';
        $cacheKey = $userkey . "_" . NameOfCache::paymentMethod->value . md5(json_encode($filters));
        return $cacheKey;
    }

    /**
     * Summary of getAll
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getAll(array $filters)
    {
        return  Cache::tags([NameOfCache::paymentMethod->value])
            ->remember($this->genKey($filters), now()->addHour(), function () use ($filters) {
                $paymentMethod = PaymentMethod::query();
                return $this->applyFilters($paymentMethod, $filters);
            });
    }

    /**
     * Summary of store
     * @param array $data
     */
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $paymentMethod = PaymentMethod::create($data);
            Cache::tags([NameOfCache::paymentMethod->value])->flush();
            return $paymentMethod;
        }, 5);
    }

    /**
     * Summary of get
     * @param PaymentMethod $paymentMethod
     * @return PaymentMethod
     */
    public function get(PaymentMethod $paymentMethod)
    {
        return $paymentMethod;
    }
    /**
     * Summary of update
     * @param PaymentMethod $paymentMethod
     * @param array $data
     */
    public function update(PaymentMethod $paymentMethod, array $data)
    {
        return DB::transaction(function () use ($paymentMethod, $data) {
            $paymentMethod->update($data);
            Cache::tags([NameOfCache::paymentMethod->value])->flush();
            return $paymentMethod;
        });
    }

    /**
     * Summary of destroy
     * @param PaymentMethod $paymentMethod
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        return DB::transaction(function () use ($paymentMethod) {

            $paymentMethod->deleted_data = [
                'name' => $paymentMethod->name,
                'description' => $paymentMethod->description,
                'code' => $paymentMethod->code,
                'type' => $paymentMethod->type,
                'is_active' => $paymentMethod->is_active, 
                'created_at' => $paymentMethod->created_at?->toDateTimeString(),
                'updated_at' => $paymentMethod->updated_at?->toDateTimeString(),
            ];

            $success = $paymentMethod->delete();

            Cache::tags([NameOfCache::paymentMethod->value])->flush();

            return $success;
        });
    }

    /**
     * Summary of afterCreated
     * @param PaymentMethod $paymentMethod
     * @return void
     */
    public function afterCreated(PaymentMethod $paymentMethod)
    {
        $adminUsers = User::wherehas('roles', function ($query) {
            $query->whereIn('name', [UserRoles::Admin->value, UserRoles::Student->value, UserRoles::Instructor->value]);
        })->get();

        Notification::send(
            $adminUsers,
            new CreatePaymentMethodNotification(Auth::id(), $paymentMethod)
        );
    }

    /**
     * Summary of afterUpdated
     * @param PaymentMethod $paymentMethod
     * @return void
     */
    public function afterUpdated(PaymentMethod $paymentMethod)
    {
        $adminUsers = User::wherehas('roles', function ($query) {
            $query->whereIn('name', [UserRoles::Admin->value, UserRoles::Student->value, UserRoles::Instructor->value]);
        })->get();
        Notification::send($adminUsers, new UpdatePaymentMethodNotification(Auth::id(), $paymentMethod));
    }


    /**
     * Summary of afterDeleted
     * @param PaymentMethod $paymentMethod
     * @return void
     */
    public function afterDeletedData(array $data)
    {
        $adminUsers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', [
                UserRoles::Admin->value,
                UserRoles::Student->value,
                UserRoles::Instructor->value
            ]);
        })->get();

        Notification::send(
            $adminUsers,
            new DeletePaymentMethodNotification(Auth::id(), $data)
        );
    }
}
