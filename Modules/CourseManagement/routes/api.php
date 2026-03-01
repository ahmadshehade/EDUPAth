<?php

use Illuminate\Support\Facades\Route;
use Modules\CourseManagement\Http\Controllers\Api\V1\CategoryController;
use Modules\CourseManagement\Http\Controllers\Api\V1\CourseController;
use Modules\CourseManagement\Http\Controllers\CourseManagementController;


Route::get('/test-speed', function () {
    return response()->json(['ok' => true]);
});

Route::middleware(['auth:sanctum'])->prefix('v1/courseManagement')->group(function () {



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
});
