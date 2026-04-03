<?php

namespace Modules\Subscription\Services;

use App\Enums\NameOfCache;
use App\Traits\FilterableServiceTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Subscription\Models\SubscriptionPlan;

class SubscriptionPlanService {

    use FilterableServiceTrait;

    /**
     * Summary of generateKey
     * @param mixed $filters
     * @return string
     */
    public  function generateKey($filters) {
        ksort($filters);
        $user = Auth::user();
        $userKey = $user ? $user->id . "_" . implode($user->roles->pluck('name')->toArray()) : "guest";
        $cacheKey = $userKey . "_" . NameOfCache::SubscriptionPlan->value . md5(json_encode($filters));
        return $cacheKey;
    }

    /**
     * Summary of getAll
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public  function getAll(array $filters) {
        return Cache::tags([NameOfCache::SubscriptionPlan->value])
            ->remember($this->generateKey($filters), now()->addMinutes(2), function () use ($filters) {
                $plans = SubscriptionPlan::query()->orderBy('id', 'desc');
                return $this->applyFilters($plans, $filters);
            });
    }

    /**
     * Summary of store
     * @param array $data
     * @return SubscriptionPlan
     */
    public  function store(array $data) {
        return DB::transaction(function () use ($data) {
            $plan = SubscriptionPlan::create($data);
            if(!empty($data['course_ids'])){
                $plan->courses()->sync($data['course_ids']);
            }
            Cache::tags([NameOfCache::SubscriptionPlan->value])->flush();
            return $plan;
        }, 5);
    }

    /**
     * Summary of getPlan
     * @param SubscriptionPlan $plan
     * @return SubscriptionPlan
     */
    public  function getPlan(SubscriptionPlan $plan) {
        return $plan;
    }

    /**
     * Summary of update
     * @param SubscriptionPlan $plan
     * @param array $data
     */
    public function update(SubscriptionPlan $plan, array $data) {
        return  DB::transaction(function () use ($data, $plan) {
            $plan->update($data);
            if(isset($data['course_ids'])){
                $plan->courses()->sync($data['course_ids']);
            }
            Cache::tags([NameOfCache::SubscriptionPlan->value])->flush();
            return $plan;
        }, 5);
    }

    /**
     * Summary of destroy
     * @param SubscriptionPlan $plan
     */
    public  function destroy(SubscriptionPlan $plan) {
        return   DB::transaction(function () use ($plan) {
            $success = $plan->delete();
            Cache::tags([NameOfCache::SubscriptionPlan->value])->flush();
            return $success;
        }, 5);
    }
}
