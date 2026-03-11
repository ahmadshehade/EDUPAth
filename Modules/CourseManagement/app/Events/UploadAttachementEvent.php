<?php



namespace Modules\CourseManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UploadAttachementEvent {
    use Dispatchable, SerializesModels;

    public int $lessonId;
    public array $paths;

    public function __construct(int $lessonId, array $paths) {
        $this->lessonId = $lessonId;
        $this->paths = $paths;
    }
}