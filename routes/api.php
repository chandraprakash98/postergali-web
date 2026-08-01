<?php

use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\OfferController;
use App\Http\Controllers\API\JobController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\ReferralController;
use App\Http\Controllers\AdminAuthController;

Route::prefix('v1')->group(function () {
    Route::apiResource('offers', OfferController::class);
    Route::get('offers/search', [OfferController::class, 'search']);
    Route::get('jobs/search', [JobController::class, 'search']);
    Route::apiResource('jobs', JobController::class);
    Route::post('notifications', [AdminAuthController::class, 'storeNotification']);
    Route::post('referrals', [ReferralController::class, 'store']);
    Route::get('referrals/check', [ReferralController::class, 'check']);
    Route::get('customers/check', [CustomerController::class, 'check']);
    Route::get('customers/{customerId}/balance', [CustomerController::class, 'balance']);
});

Route::prefix('v1')->group(function () {
    Route::apiResource('plans', PlanController::class);
});


Route::post('/admin/login', [AdminAuthController::class, 'login']);