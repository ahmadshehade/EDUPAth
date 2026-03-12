<?php

use Illuminate\Support\Facades\Route;
use Modules\CourseManagement\Http\Controllers\Api\V1\CategoryController;
use Modules\CourseManagement\Http\Controllers\Api\V1\CourseController;
use Modules\CourseManagement\Http\Controllers\Api\V1\EnrollmentController;
use Modules\CourseManagement\Http\Controllers\Api\V1\LessonController;
use Modules\CourseManagement\Http\Controllers\Api\V1\SectionController;
use Modules\CourseManagement\Http\Controllers\CourseManagementController;


Route::get('/test-speed', function () {
    return response()->json(['ok' => true]);
});

Route::middleware(['auth:sanctum', 'throttle:20,1'])->prefix('v1/courseManagement')->group(function () {



    Route::middleware(['can:adminJob'])->prefix('/categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])
            ->name('categories.index');
        Route::post('/', [CategoryController::class, 'store'])
            ->name('categories.store');
        Route::get('/{category}', [CategoryController::class, 'show'])
            ->name('categories.show');
        Route::post('/{category}', [CategoryController::class, 'update'])
            ->name('categories.update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])
            ->name('categories.delete');
    });



    Route::prefix('/courses')
        ->group(function () {
            Route::get('/', [CourseController::class, 'index'])
                ->name('courses.index');
            Route::post('/', [CourseController::class, 'store'])
                ->name('courses.store');
            Route::get('/{course}', [CourseController::class, 'show'])
                ->name('courses.show');
            Route::post('/{course}', [CourseController::class, 'update'])
                ->name('courses.update');
            Route::delete('/{course}', [CourseController::class, 'destroy'])
                ->name('courses.delete');
        });



    Route::prefix('/sections')->group(function () {

        Route::get('/', [SectionController::class, 'index'])
            ->name('sections.index');
        Route::post('/', [SectionController::class, 'store'])
            ->name('sections.store');
        Route::get('/{section}', [SectionController::class, 'show'])
            ->name('sections.show');
        Route::post('/{section}', [SectionController::class, 'update'])
            ->name('sections.update');
        Route::delete('/{section}', [SectionController::class, 'destroy'])
            ->name('sections.delete');
    });




    Route::prefix('/lessons')->group(function () {

        Route::get('/', [LessonController::class, 'index'])
            ->name('lessons.index');
        Route::post('/', [LessonController::class, 'store'])
            ->name('lessons.store');
        Route::get('/{lesson}', [LessonController::class, 'show'])
            ->name('lessons.show');
        Route::post('/{lesson}', [LessonController::class, 'update'])
            ->name('lessons.update');
        Route::delete('/{lesson}', [LessonController::class, 'destroy'])
            ->name('lessons.delete');
    });


    Route::prefix('/enrollments')->group(function () {
        Route::get('/', [EnrollmentController::class, 'index'])
            ->name('enrollments.index');
        Route::post('/', [EnrollmentController::class, 'store'])
            ->name('enrollments.store');
        Route::get('/{enrollment}', [EnrollmentController::class, 'show'])
            ->name('enrollments.show');
        Route::put('/{enrollment}', [EnrollmentController::class, 'update'])
            ->name('enrollments.update');
        Route::delete('/{enrollment}', [EnrollmentController::class, 'destroy'])
            ->name('enrollments.delete');

        Route::post('/{enrollment}/lessons/{lesson}/complete', [EnrollmentController::class, 'markLessonComplete'])
            ->name('enrollments.lessons.complete');
    });
});
