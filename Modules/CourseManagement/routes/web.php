<?php

use Illuminate\Support\Facades\Route;
use Modules\CourseManagement\Http\Controllers\CourseManagementController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('coursemanagements', CourseManagementController::class)->names('coursemanagement');
});
