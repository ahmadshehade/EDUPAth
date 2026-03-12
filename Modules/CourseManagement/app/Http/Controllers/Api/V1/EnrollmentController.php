<?php

namespace Modules\CourseManagement\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Modules\CourseManagement\Http\Requests\Api\V1\Enrollment\StoreEnrollmentRequest;
use Modules\CourseManagement\Http\Requests\Api\V1\Enrollment\UpdateEnrollmentRequest;
use Modules\CourseManagement\Models\Enrollment;
use Modules\CourseManagement\Models\Lesson;
use Modules\CourseManagement\Services\EnrollmentService;

class EnrollmentController extends Controller {

    use AuthorizesRequests;
    protected EnrollmentService $enrollmentService;

    /**
     * Summary of __construct
     * @param EnrollmentService $enrollmentService
     */
    public  function __construct(EnrollmentService $enrollmentService) {
        $this->enrollmentService = $enrollmentService;
    }

    /**
     * Summary of index
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request  $request) {
        $this->authorize('viewAny', Enrollment::class);
        $filters = $request->only([
            'user_id',
            'course_id',
            'enrolled_at',
            'progress',
            'completed_at',
            'status',
            'last_accessed_at'
        ]);

        $enrollments = $this->enrollmentService->getAll($filters);
        return $this->successMessage('Successfully Get All Enrollments .', $enrollments, 200);
    }

    /**
     * Summary of store
     * @param StoreEnrollmentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreEnrollmentRequest $request) {
        $this->authorize('create', Enrollment::class);
        $enrollment = $this->enrollmentService->makeEnrollment($request->validated());
        return $this->successMessage('Successfully Make  new Enrollment .', $enrollment, 201);
    }

    /**
     * Summary of show
     * @param Enrollment $enrollment
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Enrollment $enrollment) {
        $this->authorize('view', $enrollment);
        $data = $this->enrollmentService->getEnrollment($enrollment);
        return $this->successMessage('Successfully Get Enrollment .', $data, 200);
    }
    /**
     * Summary of update
     * @param UpdateEnrollmentRequest $request
     * @param Enrollment $enrollment
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment) {
        $this->authorize('update', $enrollment);
        $data = $this->enrollmentService->update($enrollment, $request->validated());
        return $this->successMessage('Successfully Update Enrollment .', $data, 200);
    }

    /**
     * Summary of destroy
     * @param Enrollment $enrollment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Enrollment $enrollment) {
        $this->authorize('delete', $enrollment);
        $success = $this->enrollmentService->deleteEnrollment($enrollment);
        return $this->successMessage('Successfully Delete Enrollment .', $success, 200);
    }


    /**
     * Summary of markLessonComplete
     * @param Enrollment $enrollment
     * @param Lesson $lesson
     * @return \Illuminate\Http\JsonResponse
     */
    public  function markLessonComplete(Enrollment  $enrollment, Lesson $lesson) {
        $data = $this->enrollmentService->markLessonComplete($enrollment, $lesson);
        return $this->successMessage('Successfully Make Lesson Complete', $data, 200);
    }
}
