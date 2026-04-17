<?php

use Illuminate\Support\Facades\Route;
use Modules\Payments\Http\Controllers\Api\V1\InvoiceController;
use Modules\Payments\Http\Controllers\Api\V1\PaymentMethodController;

Route::middleware(['auth:sanctum', 'can:adminJob'])->prefix('v1')->group(function () {
    Route::apiResource('paymentMethods', PaymentMethodController::class);

});

Route::middleware(['auth:sanctum'])->prefix('v1/invoices')->group(function () {
    Route::get('/',[InvoiceController::class,'index'])->name('invoices.all');
    Route::get('/{invoice}',[InvoiceController::class,'show'])->name('invoices.show');
});
