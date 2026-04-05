<?php

namespace Modules\Subscription\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteSubscriptionEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public  $data;
    public $userId;
    /**
     * Create a new event instance.
     */
    public function __construct($userId,$data) {
        $this->data = $data;
        $this->userId = $userId;
    }


}
