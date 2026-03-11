<?php

namespace Modules\CourseManagement\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Modules\CourseManagement\Http\Requests\Api\V1\Courses\StoreCourseRequest;
use Modules\CourseManagement\Http\Requests\Api\V1\Courses\UpdateCourseRequest;
use Modules\CourseManagement\Models\Course;
use Modules\CourseManagement\Services\CourseService;

class CourseController extends Controller
{
    use AuthorizesRequests;
    protected CourseService $courseService;

    /**
     * Summary of __construct
     * @param CourseService $courseService
     */
    public function __construct(CourseService $courseService)
    {
      $this->courseService=$courseService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny',Course::class);
        $filters=$request->only(['level','title','description','is_published','duration_hours']);
        $courses=$this->courseService->getAll($filters);
        return $this->successMessage('Successfully Get All Courses .',$courses,200);
    }

   

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request) {
        $this->authorize('create',Course::class);
        $course=$this->courseService->makeCourse($request->validated());
        return $this->successMessage('Successfully Make New Course',$course,201);
    }

    /**
     * Show the specified resource.
     */
    public function show(Course $course)
    {
        $this->authorize('view',$course);
       $data=$this->courseService->getCourse($course);
       return $this->successMessage('Successfully Get Course .',$data,200);
    }

    

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course) {
        $this->authorize('update',$course);
        $data=$this->courseService->updateCourse($course,$request->validated());
        return $this->successMessage('Successfully Update The Course .',$data,200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course) {
        $this->authorize('delete',$course);
        $success=$this->courseService->deleteCourse($course);
        return $this->successMessage('Successfully Delete Course .',$success,200);
    }
}
