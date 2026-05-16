<?php

use App\Http\Controllers\API\OfferController;
use App\Http\Controllers\API\JobController;
use App\Http\Controllers\API\PlanController;

Route::prefix('v1')->group(function () {
    Route::apiResource('offers', OfferController::class);
    Route::apiResource('jobs', JobController::class);
});

Route::prefix('v1')->group(function () {
    Route::apiResource('plans', PlanController::class);
});