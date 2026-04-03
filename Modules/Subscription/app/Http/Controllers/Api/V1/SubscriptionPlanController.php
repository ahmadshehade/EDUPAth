<?php

namespace Modules\Subscription\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Modules\Subscription\Http\Requests\Api\V1\SubscriptionPlans\StoreSubscriptionPlanRequest;
use Modules\Subscription\Http\Requests\Api\V1\SubscriptionPlans\UpdateSubscriptionPlanRequest;
use Modules\Subscription\Models\SubscriptionPlan;
use Modules\Subscription\Services\SubscriptionPlanService;

class SubscriptionPlanController extends Controller {
    use AuthorizesRequests;
    protected SubscriptionPlanService $subscriptionPlanService;

    /**
     * Summary of __construct
     * @param SubscriptionPlanService $subscriptionPlanService
     */
    public  function __construct(SubscriptionPlanService $subscriptionPlanService) {
        $this->subscriptionPlanService = $subscriptionPlanService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $this->authorize('viewAny', SubscriptionPlan::class);
        $filters = $request->only(['name', 'description', 'price', 'interval', 'interval_count', 'is_active']);
        $plans = $this->subscriptionPlanService->getAll($filters);
        return $this->successMessage('Successfully Get All Subscription Plans', $plans, 200);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubscriptionPlanRequest $request) {
        $this->authorize('create', SubscriptionPlan::class);
        $plan = $this->subscriptionPlanService->store($request->validated());
        return $this->successMessage('Successfully Make New Subscription', $plan, 201);
    }

    /**
     * Show the specified resource.
     */
    public function show(SubscriptionPlan $subscriptionPlan) {
        $this->authorize('view', $subscriptionPlan);
        $data = $this->subscriptionPlanService->getPlan($subscriptionPlan);
        return $this->successMessage('Successfully Get Subscription Plan', $data, 200);
    }



    /**
     * Summary of update
     * @param UpdateSubscriptionPlanRequest $request
     * @param SubscriptionPlan $subscription
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSubscriptionPlanRequest $request, SubscriptionPlan $subscriptionPlan) {
        $this->authorize('update', $subscriptionPlan);
        $data = $this->subscriptionPlanService->update($subscriptionPlan,$request->validated());
        return $this->successMessage('Successfully Update Subscription ', $data, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubscriptionPlan $subscriptionPlan) {
        $this->authorize('delete', $subscriptionPlan);
        $success = $this->subscriptionPlanService->destroy($subscriptionPlan);
        return $this->successMessage('Successfully Delete Subscription', $success, 200);
    }
}
