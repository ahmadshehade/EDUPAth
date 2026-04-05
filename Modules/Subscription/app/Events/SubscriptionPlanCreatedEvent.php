<?php

namespace Modules\Subscription\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Subscription\Models\SubscriptionPlan;

class SubscriptionPlanCreatedEvent
{
    use Dispatchable, SerializesModels;

    public $subscriptionPlan;
    public $user_id;

    /**
     * Summary of __construct
     * @param mixed $user_id
     * @param SubscriptionPlan $subscriptionPlan
     */
    public function __construct($user_id, SubscriptionPlan $subscriptionPlan)
    {
        $this->subscriptionPlan = $subscriptionPlan;
        $this->user_id = $user_id;
    }
}