<?php

namespace Modules\CourseManagement\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\CourseManagement\Http\Requests\Api\V1\Categories\StoreCategoryRequest;
use Modules\CourseManagement\Http\Requests\Api\V1\Categories\UpdateCategoryRequest;
use Modules\CourseManagement\Models\Category;
use Modules\CourseManagement\Services\CategoryService;

class CategoryController extends Controller {

    protected CategoryService $categoryService;

    /**
     * Summary of __construct
     * @param CategoryService $categoryService
     */
    public function __construct(CategoryService $categoryService) {
        $this->categoryService = $categoryService;
    }
    /**
     * Summary of index
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
       $filters=$request->only(['name','parent_id']);
       $categories=$this->categoryService->getAll($filters);
       return $this->successMessage('Successfully Get All Category.',$categories,200);
    }
    /**
     * Summary of store
     * @param StoreCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCategoryRequest $request) {
       
        $category=$this->categoryService->makeCategory($request->validated());
        return $this->successMessage('Successfully Make New Category .',$category,201);
    }
    /**
     * Summary of show
     * @param Category $categoryg
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Category $category) {
        $data=$this->categoryService->getCategory($category);
       return $this->successMessage('Successfully Get The Category .',$data,200);
    }
    /**
     * Summary of update
     * @param UpdateCategoryRequest $request
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCategoryRequest $request, Category $category) {
        $data=$this->categoryService->updateCategory($category,$request->validated());
        return $this->successMessage('Successfully Update Category',$data,200);
    }

    /**
     * Summary of destroy
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Category  $category) {
       $success=$this->categoryService->deleteCategory($category);
       return $this->successMessage('Successfully Delete Category.',$success,200);
    }
}
