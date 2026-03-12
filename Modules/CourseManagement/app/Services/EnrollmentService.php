<?php

namespace Modules\CourseManagement\Services;

use App\Enums\EnrollmentStatus;
use App\Enums\NameOfCache;
use App\Traits\FilterableServiceTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\CourseManagement\Models\Enrollment;
use Modules\CourseManagement\Models\Lesson;
use Modules\CourseManagement\Models\LessonProgress;

class EnrollmentService {

    use FilterableServiceTrait;
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
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getAll(array $filters = []) {
        $user = Auth::user();
        return Cache::tags([NameOfCache::Enrollment->value])->remember($this->generateCacheKey($user, $filters), now()->addMinutes(5), function () use ($user, $filters) {
            $enrollments = Enrollment::query()->visibleToUser($user)->with(['user', 'course']);
            return $this->applyFilters($enrollments, $filters);
        });
    }

    /**
     * Summary of makeEnrollment
     * @param array $data
     */
    public function makeEnrollment(array $data) {
        return DB::transaction(function () use ($data) {
            $data['user_id'] = Auth::id();
            $data['enrolled_at'] = now();
            $data['progress'] = 0;
            $data['status'] = EnrollmentStatus::Active->value;
            $data['last_accessed_at'] = now();
            $enrollment = Enrollment::create($data);
            Cache::tags([NameOfCache::Enrollment->value])->flush();
            return $enrollment->load(['user', 'course']);
        }, 5);
    }

    /**
     * Summary of getEnrollment
     * @param Enrollment $enrollment
     * @return Enrollment
     */
    public function getEnrollment(Enrollment  $enrollment) {
        return $enrollment->load(['user', 'course']);
    }

    /**
     * Summary of update
     * @param Enrollment $enrollment
     * @param array $data
     */
    public  function update(Enrollment $enrollment, array $data) {
        return DB::transaction(function () use ($enrollment, $data) {
            $data['last_accessed_at'] = now();
            $enrollment->update($data);
            Cache::tags([NameOfCache::Enrollment->value])->flush();
            return $enrollment->load(['user', 'course']);
        }, 5);
    }

    /**
     * Summary of deleteEnrollment
     * @param Enrollment $enrollment
     */
    public  function deleteEnrollment(Enrollment $enrollment) {
        return DB::transaction(function () use ($enrollment) {
            $success = $enrollment->delete();
            Cache::tags([NameOfCache::Enrollment->value])->flush();
            return $success;
        }, 5);
    }


    /**
     * Summary of recalculateProgress
     * @param Enrollment $enrollment
     * @return void
     */
    public function recalculateProgress(Enrollment $enrollment) {
        $totalLessons = $enrollment->course
            ->sections()
            ->withCount('lessons')
            ->get()
            ->sum('lessons_count');

        $completedLessons = $enrollment->lessonProgress()
            ->whereNotNull('completed_at')
            ->count();

        $enrollment->progress = $totalLessons > 0
            ? intval(($completedLessons / $totalLessons) * 100)
            : 0;

        $enrollment->save();
    }


    /**
     * Summary of markLessonComplete
     * @param Enrollment $enrollment
     * @param Lesson $lesson
     */
    public function markLessonComplete(Enrollment  $enrollment, Lesson $lesson) {
        return DB::transaction(function () use ($enrollment, $lesson) {
            LessonProgress::updateOrCreate(
                [
                    'enrollment_id' => $enrollment->id,
                    'lesson_id' => $lesson->id,
                ],
                [
                    'completed_at' => now(),
                ]
            );
            $this->recalculateProgress($enrollment);
            if ($enrollment->progress == 100) {
                $enrollment->completed_at = now();
                $enrollment->save();
            }else{
                   $enrollment->completed_at = null;
                $enrollment->save();
            }
            Cache::tags([NameOfCache::Lesson->value, NameOfCache::Enrollment->value])->flush();
            return $enrollment->load(['user', 'course']);
        }, 5);
    }
}
