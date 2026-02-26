<?php

use Illuminate\Support\Facades\Route;
use Modules\CourseManagement\Http\Controllers\Api\V1\CategoryController;
use Modules\CourseManagement\Http\Controllers\CourseManagementController;

Route::middleware(['auth:sanctum','can:adminJob'])->prefix('v1/categories')->group(function () {
    Route::get('/',[CategoryController::class,'index'])
    ->name('categories.index');
    Route::post('/',[CategoryController::class,'store'])
    ->name('categories.store');
    Route::get('/{category}',[CategoryController::class,'show'])
    ->name('categories.show');
    Route::post('/{category}',[CategoryController::class,'update'])
    ->name('categories.update');
    Route::delete('/{category}',[CategoryController::class,'destroy'])
    ->name('categories.delete');
});
