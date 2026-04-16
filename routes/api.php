<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdController;
use App\Http\Controllers\Api\JobController;

Route::post('/job-price', [JobController::class, 'getPrice']);

Route::prefix('ads')->group(function () {
    Route::post('/', [AdController::class, 'store']);
    Route::get('/', [AdController::class, 'index']);
    Route::get('/{id}', [AdController::class, 'show']);
    Route::post('/{id}', [AdController::class, 'update']);
    Route::delete('/{id}', [AdController::class, 'destroy']);
});

Route::post('/jobs', [JobController::class, 'store']);
Route::get('/jobs', [JobController::class, 'index']);

