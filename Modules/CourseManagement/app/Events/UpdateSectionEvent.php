<?php

namespace Modules\CourseManagement\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateSectionEvent {
    use Dispatchable, InteractsWithSockets, SerializesModels,InteractsWithQueue;

    public  $section;
    public  $afterCommit = true;
    public $user_id;
    /**
     * Create a new event instance.
     */
    public function __construct($user_id, $section) {
        $this->section = $section;
        $this->user_id = $user_id;
    }
}
