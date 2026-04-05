<?php

namespace Modules\Subscription\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreateSubscriptionEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

   public $subscription;

   public $user_id;
    /**
     * Create a new event instance.
     */
    public function __construct($user_id,$subscription) {
        $this->user_id = $user_id;
        $this->subscription = $subscription;
    }

 
}
