<?php

namespace Modules\CourseManagement\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateEnrollmentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $enrollment;
    /**
     * Create a new event instance.
     */
    public function __construct($user_id,$enrollment) {
        $this->user_id = $user_id;
        $this->enrollment = $enrollment;
    }

   
}
