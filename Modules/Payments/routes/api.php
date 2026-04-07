<?php

use Illuminate\Support\Facades\Route;
use Modules\Payments\Http\Controllers\Api\V1\PaymentMethodController;

Route::middleware(['auth:sanctum', 'can:adminJob'])->prefix('v1')->group(function () {
    Route::apiResource('paymentMethods', PaymentMethodController::class);
});
