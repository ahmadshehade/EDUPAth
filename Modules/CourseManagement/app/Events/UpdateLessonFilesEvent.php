<?php

namespace Modules\CourseManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateLessonFilesEvent
{
    use Dispatchable, SerializesModels;

    public int $lessonId;
    public array $newPaths;     
    public array $oldMediaIds; 
    
    public  $afterCommit=true;

    public function __construct(int $lessonId, array $newPaths, array $oldMediaIds)
    {
        $this->lessonId = $lessonId;
        $this->newPaths = $newPaths;
        $this->oldMediaIds = $oldMediaIds;
    }
}