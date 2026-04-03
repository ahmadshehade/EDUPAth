<?php

namespace Modules\Subscription\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Modules\Subscription\Http\Requests\Api\V1\Subscriptions\StoreSubscriptionRequest;
use Modules\Subscription\Http\Requests\Api\V1\Subscriptions\UpdateSubscriptionRequest;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Services\SubscriptionService;

class SubscriptionController extends Controller {
    use AuthorizesRequests;
    protected SubscriptionService $subscription;
    public  function __construct(SubscriptionService $subscription) {
        $this->subscription = $subscription;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {

        $filters = $request->only(['user_id', 'plan_id', 'starts_at', 'ends_at', 'status']);
        $subscriptions = $this->subscription->getAll($filters);
        return $this->successMessage('Successfully Get All Subscriptions .', $subscriptions, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubscriptionRequest $request) {
        $subscription = $this->subscription->store($request->validated());
        return $this->successMessage('Successfully Make New Subscription .', $subscription, 201);
    }

    /**
     * Show the specified resource.
     */
    public function show(Subscription $subscription) {
        $data = $this->subscription->get($subscription);
        return $this->successMessage('Successfully Get Subscription .', $data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriptionRequest $request, Subscription $subscription) {
        $data = $this->subscription->update($subscription, $request->validated());
        return $this->successMessage('Successfully Update Subscription .', $data, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription) {
        $success = $this->subscription->destroy($subscription);
        return $this->successMessage('Successfully Delete Subscription .', $success, 200);
    }
}
