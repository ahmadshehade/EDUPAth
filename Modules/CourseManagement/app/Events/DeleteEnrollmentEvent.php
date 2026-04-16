<?php

namespace Modules\CourseManagement\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteEnrollmentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $data;
    /**
     * Create a new event instance.
     */
    public function __construct($user_id,$data) {
        $this->user_id = $user_id;
        $this->data = $data;
    }

    
}
