<?php

namespace Modules\CourseManagement\Services;

use App\Enums\NameOfCache;
use App\Traits\FilterableServiceTrait;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\CourseManagement\Events\CreateSectionEvent;
use Modules\CourseManagement\Events\DeleteSectionEvent;
use Modules\CourseManagement\Events\UpdateSectionEvent;
use Modules\CourseManagement\Models\Course;
use Modules\CourseManagement\Models\Section;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SectionService {

    use  FilterableServiceTrait;
    /**
     * Summary of generateCacheKey
     * @param mixed $user
     * @param mixed $filters
     * @return string
     */
    public function generateCacheKey($user, $filters) {
        ksort($filters);
        $userKey = $user
            ? $user->id . '_' . md5(json_encode($user->roles->pluck('name')))
            : 'guest';
        $cacheKey = $userKey . "_" . md5(json_encode($filters));
        return $cacheKey;
    }

    /**
     * Summary of getAll
     * @param array $data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getAll(array  $data) {
        $user = Auth::user();
        return Cache::tags([NameOfCache::Section->value])
            ->remember($this->generateCacheKey($user, $data), now()->addMinute(), function () use ($data, $user) {
                $setions = Section::query()->visibleForSection($user)->with('course.instructor')->orderBy('order', 'asc');
                return $this->applyFilters($setions, $data);
            });
    }

    /**
     * Summary of makeSection
     * @param array $data
     */
    public function makeSection(array $data) {
        try {
            return DB::transaction(function () use ($data) {
                $course = Course::with('instructor')->findOrFail($data['course_id']);
                if (!$course->instructor) {
                    throw new HttpException(400, 'Course has no instructor.');
                }
                if ($course->instructor->id !== Auth::id() && !$course->is_published) {
                    throw new AuthorizationException('You are not allowed to create a section for this course.');
                }
                $section = Section::create($data);
                Cache::tags([NameOfCache::Section->value])->flush();
                event(new CreateSectionEvent($section, Auth::id()));
                return $section->load(['course.instructor']);
            }, 5);
        } catch (Exception $e) {
            Log::error('Fail Make Section: ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Summary of getSection
     * @param Section $section
     * @return Section
     */
    public function getSection(Section $section) {
        return $section->load(['course.instructor']);
    }

    /**
     * Summary of update
     * @param Section $section
     * @param array $data
     */
    public function update(Section  $section, array $data) {
        try {
            return DB::transaction(function () use ($section, $data) {
                if (isset($data['course_id'])) {
                    $course = Course::findOrFail($data['course_id']);
                    if (!$course->instructor) {
                        throw new HttpException(400, 'Course has no instructor.');
                    }
                    if ($course->instructor->id !== Auth::id() && !$course->is_published) {
                        throw new AuthorizationException('You are not allowed to update a section for this course.');
                    }
                }
                unset($data['slug']);
                $section->update($data);
                Cache::tags([NameOfCache::Section->value])->flush();
                event(new UpdateSectionEvent(Auth::id(), $section));
                return $section->load(['course.instructor']);
            }, 5);
        } catch (Exception  $e) {
            Log::error('Fail To Update Section :' . $e->getMessage());
            throw  $e;
        }
    }

    /**
     * Summary of deleteSection
     * @param Section $section
     */
    public function deleteSection(Section $section) {
        return DB::transaction(function () use ($section) {
            $data = [
                'teacher_id'   => $section->course->instructor_id,
                'student_ids'  => $section->course->students()->pluck('users.id')->toArray(),
                'title' => [
                    'ar' => $section->getTranslation('title', 'ar'),
                    'en' => $section->getTranslation('title', 'en'),
                ],
                'is_published' => $section->is_published,
            ];
            $success = $section->delete();
            if ($success) {
                event(new DeleteSectionEvent($data, Auth::id()));
            }
            Cache::tags([NameOfCache::Section->value])->flush();
            return $success;
        });
    }
}
