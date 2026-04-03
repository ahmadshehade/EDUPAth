<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\Http\Controllers\Api\V1\SubscriptionPlanController;
use Modules\Subscription\Http\Controllers\SubscriptionController;

Route::prefix('v1/subscriptions')->middleware(['auth:sanctum','throttle:60,1'])->group(function(){
    Route::middleware(['can:adminJob'])
    ->apiResource('subscriptionPlans',SubscriptionPlanController::class);
    //
    Route::get('/',[SubscriptionController::class,'index'])->name('subscription.index');
    Route::post('/',[SubscriptionController::class,'store'])->name('subscription.store');
    Route::get('/{subscription}',[SubscriptionController::class,'show'])->name('subscriptions.get');
    Route::put('/{subscription}',[SubscriptionController::class,'update'])->name('subscription.update');
    Route::delete('/{subscription}',[SubscriptionController::class,'destroy'])->name('subscription.delete');
});


