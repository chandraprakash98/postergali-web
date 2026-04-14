<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdAdminController;
use App\Http\Controllers\Admin\JobAdminController;

Route::get('/', function () {
    return view('postergalihome');
});

Route::post('/admin/ads/{id}/update-all', [AdAdminController::class, 'updateAll']);
Route::prefix('admin')->group(function () {
    Route::get('/ads', [AdAdminController::class, 'index']);
    Route::get('/ads/{id}', [AdAdminController::class, 'show']);
    Route::post('/ads/{id}/approve', [AdAdminController::class, 'approve']);
    Route::post('/ads/{id}/reject', [AdAdminController::class, 'reject']);
});

Route::post('/ads/{id}/update-range', [AdAdminController::class, 'updateRange']);