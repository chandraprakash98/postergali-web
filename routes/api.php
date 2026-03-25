<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdController;
use App\Http\Controllers\Api\JobController;

Route::prefix('ads')->group(function () {
    Route::post('/', [AdController::class, 'store']);
    Route::get('/', [AdController::class, 'index']);
    Route::get('/{id}', [AdController::class, 'show']);
    Route::post('/{id}', [AdController::class, 'update']);
    Route::delete('/{id}', [AdController::class, 'destroy']);
});


Route::prefix('jobs')->group(function () {
    Route::get('/', [JobController::class, 'index']);        // List all jobs
    Route::post('/', [JobController::class, 'store']);       // Create a job
    Route::get('{id}', [JobController::class, 'show']);      // Get job by ID
    Route::put('{id}', [JobController::class, 'update']);    // Update job
    Route::delete('{id}', [JobController::class, 'destroy']); // Delete job
});
