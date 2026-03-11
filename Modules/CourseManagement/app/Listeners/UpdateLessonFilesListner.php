<?php

namespace Modules\CourseManagement\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\CourseManagement\Events\UpdateLessonFilesEvent;
use Modules\CourseManagement\Models\Lesson;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UpdateLessonFilesListner implements ShouldQueue
{

    /**
     * Summary of handle
     * @param UpdateLessonFilesEvent $event
     * @return void
     */
    public function handle(UpdateLessonFilesEvent $event)
    {
        $lesson = Lesson::find($event->lessonId);

        if (! $lesson) {
            Log::error("UpdateLessonFilesListener: lesson {$event->lessonId} not found.");
            return;
        }

        $newPaths = $event->newPaths;
        $oldMediaIds = $event->oldMediaIds;

        try {
            Log::info("UpdateLessonFilesListener started for lesson {$lesson->id}", [
                'new_paths' => $newPaths,
                'old_media_ids' => $oldMediaIds
            ]);

            if (!empty($oldMediaIds)) {
                Media::whereIn('id', $oldMediaIds)->get()->each->delete();
                Log::info("Deleted old media", ['ids' => $oldMediaIds]);
            }

            foreach ($newPaths['videos'] ?? [] as $relPath) {
                if (Storage::disk('public')->exists($relPath)) {
                    $lesson->addMediaFromDisk($relPath, 'public')->toMediaCollection('videos');
                    Storage::disk('public')->delete($relPath);
                    Log::info("Video added: {$relPath}");
                } else {
                    Log::error("Video file missing: {$relPath}");
                }
            }

            foreach ($newPaths['files'] ?? [] as $relPath) {
                if (Storage::disk('public')->exists($relPath)) {
                    $lesson->addMediaFromDisk($relPath, 'public')->toMediaCollection('files');
                    Storage::disk('public')->delete($relPath);
                    Log::info("File added: {$relPath}");
                } else {
                    Log::error("File missing: {$relPath}");
                }
            }

            foreach ($newPaths['assignments'] ?? [] as $relPath) {
                if (Storage::disk('public')->exists($relPath)) {
                    $lesson->addMediaFromDisk($relPath, 'public')->toMediaCollection('assignments');
                    Storage::disk('public')->delete($relPath);
                    Log::info("Assignment added: {$relPath}");
                } else {
                    Log::error("Assignment missing: {$relPath}");
                }
            }

            Log::info("UpdateLessonFilesListener finished for lesson {$lesson->id}");
        } catch (\Exception $e) {
            Log::error("UpdateLessonFilesListener failed: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}