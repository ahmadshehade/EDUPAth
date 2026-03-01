<?php

namespace Modules\CourseManagement\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateCourseEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $course;

    public $user_id;
    /**
     * Create a new event instance.
     */
    public function __construct( $course,$user_id) {
        $this->user_id=$user_id;
        $this->course=$course;
    }

    
}
