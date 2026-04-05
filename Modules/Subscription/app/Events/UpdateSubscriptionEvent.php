<?php

namespace Modules\Subscription\Events;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateSubscriptionEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public  $subscription;
    public $userId;
    /**
     * Create a new event instance.
     */
    public function __construct($userId, $subscription)
    {
        $this->userId = $userId;
        $this->subscription = $subscription;
    }


}
