<?php

namespace Modules\Subscription\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteSubscriptionPlanEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $data;

    public $user_id;
    /**
     * Create a new event instance.
     */
    public function __construct($user_id,array $data) {
        $this->data = $data;
        $this->user_id = $user_id;
    }

  
}
