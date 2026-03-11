<?php

namespace Modules\CourseManagement\Services;

use Illuminate\Support\Facades\Storage;
use App\Enums\LessonType;
use App\Enums\NameOfCache;
use App\Traits\FilterableServiceTrait;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\CourseManagement\Events\UpdateLessonFilesEvent;
use Modules\CourseManagement\Events\UploadAttachementEvent;
use Modules\CourseManagement\Models\Lesson;
use Modules\CourseManagement\Models\Section;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LessonService {

    use FilterableServiceTrait;
    /**
     * Summary of genKey
     * @param mixed $user
     * @param mixed $filters
     * @return string
     */
    public function genKey($filters) {
        $user = Auth::user();
        $userkey = $user ? $user->id . '_' . json_encode($user->roles()->pluck('name')->toArray()) : "guest";
        $cacheKey = $userkey . '_' . ((!$filters ? "" : md5(json_encode($filters))));
        return $cacheKey;
    }

    /**
     * Summary of getAll
     * @param array $data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getAll(array $data) {
        $user = Auth::user();
        return Cache::tags([NameOfCache::Lesson->value])
            ->remember($this->genKey($data), now()->addMinutes(3), function () use ($data, $user) {
                $lessons = Lesson::query()->visibleForLesson($user)->with(['section', 'section.course.instructor', 'media'])->orderBy('order');
                return $this->applyFilters($lessons, $data);
            });
    }

    /**
     * Summary of makeLesson
     * @param array $data
     */


    public function makeLesson(array $data) {
        try {

            $section = Section::with(['course.instructor'])->findOrFail($data['section_id']);

            if (!$section->is_published) {
                throw new HttpException(422, 'Publish Section Is False');
            }

            if ($section->course->instructor->id != Auth::id()) {
                throw new AuthorizationException('You are not allowed to create a lesson for this section.');
            }

            $paths = [
                'videos' => [],
                'files' => [],
                'assignments' => [],
            ];

            foreach ($data['videos'] ?? [] as $video) {
                $paths['videos'][] = $video->getPathname();
            }

            foreach ($data['file'] ?? [] as $file) {
                $paths['files'][] = $file->getPathname();
            }

            foreach ($data['assignment_file'] ?? [] as $assignment) {
                $paths['assignments'][] = $assignment->getPathname();
            }

            $lesson = DB::transaction(function () use ($data, $section) {

                $lesson = Lesson::create($data);

                if ($data['type'] === LessonType::Live->value) {

                    $meetingName = 'lesson-' . Str::random(10);

                    $lesson->update([
                        'live_url' => "https://meet.jit.si/{$meetingName}",
                        'live_status' => 'upcoming',
                        'live_start_at' => now()
                    ]);

                    Log::info("Live URL generated for lesson {$lesson->id}");
                }

                return $lesson;
            });

            DB::afterCommit(function () use ($lesson, $paths) {
                event(new UploadAttachementEvent($lesson->id, $paths));
            });

            Cache::tags([NameOfCache::Lesson->value])->flush();

            return $lesson;
        } catch (Exception $e) {

            Log::error('Fail To Make Lesson:' . $e->getMessage());

            throw $e;
        }
    }

    /**
     * Summary of getLesson
     * @param Lesson $lesson
     * @return Lesson
     */
    public  function getLesson(Lesson $lesson) {
        return $lesson->load(['section.course.instructor', 'media']);
    }

    /**
     * Summary of update
     * @param Lesson $lesson
     * @param array $data
     */

    public function update(Lesson $lesson, array $data) {
        try {
            return DB::transaction(function () use ($lesson, $data) {
                $lesson->update($data);

                $newPaths = [
                    'videos' => [],
                    'files' => [],
                    'assignments' => [],
                ];
                foreach (($data['videos'] ?? []) as $video) {
                    if ($video instanceof UploadedFile) {
                        $newPaths['videos'][] = $video->getPathname();
                    }
                }

                foreach (($data['file'] ?? []) as $file) {
                    if ($file instanceof UploadedFile) {
                        $newPaths['files'][] = $file->getPathname();
                    }
                }

                foreach (($data['assignment_file'] ?? []) as $assignment) {
                    if ($assignment instanceof UploadedFile) {
                        $newPaths['assignments'][] = $assignment->getPathname();
                    }
                }
                $oldMediaIds = $lesson->media()
                    ->whereIn('collection_name', ['videos', 'files', 'assignments'])
                    ->pluck('id')
                    ->toArray();


                DB::afterCommit(function () use ($lesson, $newPaths, $oldMediaIds) {

                    if (
                        !empty($newPaths['videos']) ||
                        !empty($newPaths['files']) ||
                        !empty($newPaths['assignments'])
                    ) {

                        event(new UpdateLessonFilesEvent(
                            $lesson->id,
                            $newPaths,
                            $oldMediaIds
                        ));
                    }
                });
                Cache::tags([NameOfCache::Lesson->value])->flush();
                return $lesson;
            }, 5);
        } catch (Exception $e) {

            Log::error('Fail To Update Lesson: ' . $e->getMessage());

            throw $e;
        }
    }

    /**
     * Summary of destroyLesson
     * @param Lesson $lesson
     */
    public  function destroyLesson(Lesson $lesson) {
        return DB::transaction(
            function () use ($lesson) {
                $success = $lesson->delete();
                Cache::tags([NameOfCache::Lesson->value])->flush();
                return $success;
            },
            5
        );
    }
}
