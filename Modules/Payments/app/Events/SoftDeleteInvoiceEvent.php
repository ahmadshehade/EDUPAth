<?php

namespace Modules\Payments\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SoftDeleteInvoiceEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public  $actor_id;

    public  $owner_id;
    public  $invoices;
    /**
     * Create a new event instance.
     */
    public function __construct($actor_id, $owner_id, $invoices)
    {
        $this->actor_id = $actor_id;
        $this->owner_id = $owner_id;
        $this->invoices = $invoices;
    }
}
