<?php

namespace Modules\Payments\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreateInvoiceForSubscriptionEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public  $invoice;
    /**
     * Create a new event instance.
     */
    public function __construct($invoice) {
        $this->invoice = $invoice;
    }


}
