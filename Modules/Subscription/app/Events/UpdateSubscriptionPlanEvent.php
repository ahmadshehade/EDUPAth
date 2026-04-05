<?php

namespace Modules\Subscription\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateSubscriptionPlanEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $subscriptionPlan;

    public $userId;
    /**
     * Create a new event instance.
     */
    public function __construct($userId,$subscriptionPlan) {
        $this->subscriptionPlan = $subscriptionPlan;
        $this->userId = $userId;
    }

  
}
