<?php

namespace Modules\CourseManagement\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\HandlesChunkUpload;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Modules\CourseManagement\Http\Requests\Api\V1\Lesson\StoreLessonRequest;
use Modules\CourseManagement\Http\Requests\Api\V1\Lesson\UpdateLessonRequest;
use Modules\CourseManagement\Models\Lesson;
use Modules\CourseManagement\Services\LessonService;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class LessonController extends Controller {
    use AuthorizesRequests, HandlesChunkUpload;
    protected LessonService $lessonService;

    public  function __construct(LessonService $lessonService) {
        $this->lessonService = $lessonService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $this->authorize('viewAny', Lesson::class);
        $filters = $request->only(['title', 'section_id', 'order']);
        $lessons = $this->lessonService->getAll($filters);
        return $this->successMessage('Successfully Get All Lessons ', $lessons, 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLessonRequest $request) {

        $this->authorize('create', Lesson::class);
        $data = $request->validated();
        $chunkFiles = $this->handleChunkUpload($request, ['videos', 'file', 'assignment_file'], '/mnt/ramdisk');
        $data = array_merge($data, $chunkFiles);
        $lesson = $this->lessonService->makeLesson($data);
        return $this->successMessage('Successfully Make New Lesson', $lesson, 201);
    }

    /**
     * Show the specified resource.
     */
    public function show(Lesson $lesson) {
        $this->authorize('view', $lesson);
        $data = $this->lessonService->getLesson($lesson);
        return $this->successMessage('Successfully Get Lesson', $data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLessonRequest $request, Lesson $lesson) {
        $this->authorize('update', $lesson);
        $data = $request->validated();
        $chunkFiles = $this->handleChunkUpload($request, ['videos', 'file', 'assignment_file'], '/mnt/ramdisk');
        $data = array_merge($data, $chunkFiles);
        $data = $this->lessonService->update($lesson, $data);
        return $this->successMessage('Successfully Update Lesson', $data, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson) {
        $this->authorize('delete', $lesson);
        $data = $this->lessonService->destroyLesson($lesson);
        return $this->successMessage('Successfully Delete Lesson', $data, 200);
    }
}
