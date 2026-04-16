<?php

namespace Modules\Payments\Events;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreateInvoiceForInrollmentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;

    public  $invoice;
    /**
     * Create a new event instance.
     */
    public function __construct($user_id,$invoice) {
        $this->user_id = $user_id;
        $this->invoice = $invoice;
    }

  
}
