<?php

namespace Modules\CourseManagement\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\CourseManagement\Events\UploadAttachementEvent;
use Modules\CourseManagement\Models\Lesson;
use Illuminate\Support\Str;

class UploadAttachementListener implements ShouldQueue {
    public function handle(UploadAttachementEvent $event) {
        $lesson = Lesson::find($event->lessonId);

        if (! $lesson) {
            Log::error("UploadAttachementListener: lesson {$event->lessonId} not found.");
            return;
        }

        $paths = $event->paths;

        try {
            Log::info("UploadAttachementListener started for lesson {$lesson->id}", ['paths' => $paths]);


            foreach ($paths['videos'] ?? [] as $relPath) {
                if (Storage::disk('public')->exists($relPath)) {
                    $lesson->addMediaFromDisk($relPath, 'public')->toMediaCollection('videos');
                    Storage::disk('public')->delete($relPath);
                    Log::info("Video added successfully: {$relPath}");
                } else {
                    Log::error("Video file missing on public disk: {$relPath}");
                }
            }
            foreach ($paths['files'] ?? [] as $relPath) {
                if (Storage::disk('public')->exists($relPath)) {
                    $lesson->addMediaFromDisk($relPath, 'public')->toMediaCollection('files');
                    Storage::disk('public')->delete($relPath);
                    Log::info("File added successfully: {$relPath}");
                } else {
                    Log::error("File missing on public disk: {$relPath}");
                }
            }
            foreach ($paths['assignments'] ?? [] as $relPath) {
                if (Storage::disk('public')->exists($relPath)) {
                    $lesson->addMediaFromDisk($relPath, 'public')->toMediaCollection('assignments');
                    Storage::disk('public')->delete($relPath);
                    Log::info("Assignment added successfully: {$relPath}");
                } else {
                    Log::error("Assignment file missing on public disk: {$relPath}");
                }
            }
            Log::info("UploadAttachementListener finished for lesson {$lesson->id}, media_count=" . $lesson->media()->count());
        } catch (\Exception $e) {
            Log::error("Fail to upload lesson files for lesson #{$lesson->id}: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }
}
