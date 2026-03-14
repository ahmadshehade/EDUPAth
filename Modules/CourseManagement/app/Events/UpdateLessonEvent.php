<?php

namespace Modules\CourseManagement\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateLessonEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    
    public $afterCommit=true;
    public $lesson;
    /**
     * Create a new event instance.
     */
    public function __construct($lesson,$user_id) {
        $this->user_id=$user_id;
        $this->lesson=$lesson;
    }

 
}
