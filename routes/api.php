<?php

use App\Http\Controllers\API\OfferController;
use App\Http\Controllers\API\JobController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\AdminAuthController;

Route::prefix('v1')->group(function () {
    Route::apiResource('offers', OfferController::class);
    Route::get('offers/search', [OfferController::class, 'search']);
    Route::get('jobs/search', [JobController::class, 'search']);
    Route::apiResource('jobs', JobController::class);
});

Route::prefix('v1')->group(function () {
    Route::apiResource('plans', PlanController::class);
});


Route::post('/admin/login', [AdminAuthController::class, 'login']);