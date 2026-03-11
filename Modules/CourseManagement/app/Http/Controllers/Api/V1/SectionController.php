<?php

namespace Modules\CourseManagement\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Modules\CourseManagement\Http\Requests\Api\V1\Sections\StoreSectionRequest;
use Modules\CourseManagement\Http\Requests\Api\V1\Sections\UpdateSectionRequest;
use Modules\CourseManagement\Models\Section;
use Modules\CourseManagement\Services\SectionService;

class SectionController extends Controller {
    protected SectionService $sectionService;

    use AuthorizesRequests;
    /**
     * Summary of __construct
     * @param SectionService $sectionService
     */
    public function __construct(SectionService $sectionService) {
        $this->sectionService = $sectionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $this->authorize('viewAny', Section::class);
        $filters = $request->only(['name', 'order', 'title', 'is_published']);
        $sections = $this->sectionService->getAll($filters);
        return $this->successMessage('Successfully Get All Sections', $sections, 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSectionRequest $request) {
        $this->authorize('create', Section::class);
        $sections = $this->sectionService->makeSection($request->validated());
        return $this->successMessage('Successfully Make New Section .', $sections, 201);
    }

    /**
     * Show the specified resource.
     */
    public function show(Section $section) {
        $this->authorize('view', $section);
        $data = $this->sectionService->getSection($section);
        return $this->successMessage('Successfully Get Section .', $data, 200);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSectionRequest $request, Section $section) {
        $this->authorize('update', $section);
        $data = $this->sectionService->update($section, $request->validated());
        return $this->successMessage('Successfully Update Section .', $data, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section) {
        $this->authorize('delete', $section);
        $success = $this->sectionService->deleteSection($section);
        return $this->successMessage('Successfully Delete Section', $success, 204);
    }
}
