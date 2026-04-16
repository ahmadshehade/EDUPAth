<?php

namespace Modules\CourseManagement\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreateEnrollmentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public  $enrollment;

    public  $user_id;
    /**
     * Create a new event instance.
     */
    public function __construct($user_id,$enrollment) {
        $this->enrollment = $enrollment;
        $this->user_id = $user_id;
    }

}
