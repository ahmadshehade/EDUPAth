<?php

namespace Modules\CourseManagement\Services;

use App\Enums\NameOfCache;
use App\Traits\FilterableServiceTrait;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\CourseManagement\Events\CreateCourseEvent;
use Modules\CourseManagement\Events\UpdateCourseEvent;
use Modules\CourseManagement\Models\Course;

class CourseService {

    use FilterableServiceTrait;

    /**
     * Summary of makeKey
     * @param mixed $filters
     * @return string
     */
    protected function makeKey($user,$filters=[]) {
        
        ksort($filters);
        $userKey = $user ? $user->id . '-' . $user->roles->pluck('id')->sort()->implode('-') : 'guest';
        return NameOfCache::Course->value.$userKey . md5(json_encode($filters));
    }

    /**
     * Summary of getAll
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public  function getAll(array $filters = []) {
        return Cache::tags([NameOfCache::Course->value])
            ->remember($this->makeKey(Auth::user(),$filters), now()->addMinutes(2), function () use ($filters) {
                $courses = Course::query()->forVisibleCourse(Auth::user())->with(['instructor', 'category']);
                return $this->applyFilters($courses, $filters);
            });
    }

    /**
     * Summary of makeCourse
     * @param array $data
     */
    public function makeCourse(array $data) {
        return DB::transaction(function () use ($data) {
            $data['instructor_id'] = Auth::user()->id;
            $course = Course::create($data);
            if (!empty($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $media) {
                    if ($media instanceof UploadedFile) {
                        $course->addMedia($media)
                            ->toMediaCollection('course');
                    }
                }
            }
            event(new CreateCourseEvent($course, Auth::user()->id));
            Cache::tags([NameOfCache::Course->value])->flush();
            return $course->load(['instructor', 'category']);
        }, 5);
    }

    /**
     * Summary of getCourse
     * @param Model $model
     * @return Model
     */
    public function getCourse(Model $model) {
        return $model->load(['instructor', 'category']);
    }

    /**
     * Summary of updateCourse
     * @param Model $model
     * @param array $data
     */
    public function updateCourse(Model $model, array $data) {
        return DB::transaction(function () use ($model, $data) {
            $model->update($data);
            if (isset($data['images']) && is_array($data['images'])) {
                if ($model->hasMedia('course')) {
                    $model->clearMediaCollection('course');
                }
                foreach ($data['images'] as $image) {
                    if ($image instanceof UploadedFile) {
                        $model->addMedia($image)->toMediaCollection('course');
                    }
                }
            }
            event(new UpdateCourseEvent($model,Auth::user()->id));
            Cache::tags([NameOfCache::Course->value])->flush();
            return $model->load(['instructor', 'category']);
        }, 5);
    }

    /**
     * Summary of deleteCourse
     * @param Model $model
     */
    public  function deleteCourse(Model  $model) {
        return DB::transaction(function () use ($model) {
            if ($model->hasMedia('course')) {
                $model->clearMediaCollection('course');
            }
            $success = $model->delete();
            Cache::tags([NameOfCache::Course->value])->flush();
            return $success;
        }, 5);
    }
}
