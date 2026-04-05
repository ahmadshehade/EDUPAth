<?php

namespace Modules\Subscription\Services;

use App\Enums\NameOfCache;
use App\Enums\SubscriptionStatus;
use App\Traits\FilterableServiceTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Subscription\Events\CreateSubscriptionEvent;
use Modules\Subscription\Events\DeleteSubscriptionEvent;
use Modules\Subscription\Events\UpdateSubscriptionEvent;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Models\SubscriptionPlan;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SubscriptionService
{

    use FilterableServiceTrait;
    /**
     * Summary of geneKey
     * @param array $filters
     * @return string
     */
    public  function geneKey(array $filters = [])
    {
        $user = Auth::user();
        $userKey = $user ? $user->id . "_" . implode($user->roles->pluck('name')->toArray()) : "guest";
        $cacheKey = $userKey . "_" . NameOfCache::Subscription->value . md5(json_encode($filters));
        return $cacheKey;
    }

    /**
     * Summary of getAll
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getAll(array $filters)
    {
        return Cache::tags([NameOfCache::Subscription->value])->remember($this->geneKey($filters), now()->addMinute(), function () use ($filters) {
            $query = Subscription::query()->filterable(Auth::user())->with(['user', 'plan']);
            return $this->applyFilters($query, $filters);
        });
    }

    /**
     * Summary of store
     * @param array $data
     */
    public function store(array $data)
    {
        return  DB::transaction(function () use ($data) {
            $data['user_id'] = Auth::id();
            $plan = SubscriptionPlan::where('id', $data['plan_id'])
                ->firstOrFail();
            if ($plan->is_active === false) {
                throw new HttpException(403, 'Plan Not Active yet.');
            }
            $existing = Subscription::where('user_id', $data['user_id'])
                ->where('plan_id', $plan->id)
                ->whereIn('status', [SubscriptionStatus::Active->value, SubscriptionStatus::Pending->value])
                ->first();
            if ($existing) {
                throw new HttpException(403, 'You are already subscribed to this plan.');
            }
            $data['starts_at'] = now();
            $data['ends_at'] = $this->calEndDate($plan->interval, $plan->interval_count);
            $data['status'] = $data['status'] ?? SubscriptionStatus::Pending->value;
            $subscription =  Subscription::create($data);
            Cache::tags([NameOfCache::Subscription->value])->flush();
            DB::afterCommit(function () use ($subscription, $data) {
                event(new CreateSubscriptionEvent($data['user_id'], $subscription));
            });
            return $subscription;
        }, 5);
    }

    /**
     * Summary of get
     * @param Subscription $subscription
     * @return Subscription
     */
    public  function get(Subscription $subscription)
    {
        return $subscription->load(['user', 'plan']);
    }

    /**
     * Summary of update
     * @param Subscription $subscription
     * @param array $data
     */
    public  function update(Subscription $subscription, array $data)
    {
        return DB::transaction(function () use ($subscription, $data) {
            $plan = SubscriptionPlan::where('id', $data['plan_id'] ?? $subscription->plan->id)
                ->firstOrFail();
            if ($plan->is_active === false) {
                throw new HttpException(403, 'Plan Not Active yet.');
            }
            $existing = Subscription::where('user_id', Auth::id())
                ->where('plan_id', $plan->id)
                ->whereIn('status', [SubscriptionStatus::Active->value, SubscriptionStatus::Pending->value])
                ->first();

            if ($existing) {
                throw new HttpException(403, 'You are already subscribed to this plan.');
            }
            $data['starts_at'] = now();
            $data['ends_at'] = $this->calEndDate($plan->interval, $plan->interval_count);
            $subscription->update($data);
            Cache::tags([NameOfCache::Subscription->value])->flush();
            DB::afterCommit(function () use ($subscription) {
                event(new UpdateSubscriptionEvent(Auth::id(), $subscription));
            });
            return $subscription->load(['user', 'plan']);
        });
    }

    /**
     * Summary of destroy
     * @param Subscription $subscription
     */
    public function destroy(Subscription $subscription)
    {
        return DB::transaction(function () use ($subscription) {
            $data = [
                'user_id' => $subscription->user->id,
                'ends_date' => $subscription->ends_date,
                'plan' => $subscription->plan->name,
            ];
            $success = $subscription->delete();
            Cache::tags([NameOfCache::Subscription->value])->flush();
            DB::afterCommit(function () use ($data) {
                event(new DeleteSubscriptionEvent(Auth::id(), $data));
            });

            return $success;
        });
    }

    /**
     * Summary of calEndDate
     * @param mixed $interval
     * @param mixed $interval_count
     * @return \Carbon\CarbonInterface|null
     */
    public function calEndDate($interval, $interval_count)
    {
        switch ($interval) {
            case "day":
                return now()->addDays($interval_count);
            case "week":
                return now()->addWeeks($interval_count);
            case "month":
                return now()->addMonths($interval_count);
            case "year":
                return now()->addYears($interval_count);
            default:
                return null;
        }
    }
}
