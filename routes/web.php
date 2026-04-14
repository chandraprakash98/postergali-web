<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdAdminController;
use App\Http\Controllers\Admin\JobAdminController;
use App\Http\Controllers\AdminLoginController;

Route::get('/', function () {
    return view('postergalihome');
});

// Admin Login Routes
Route::get('/admin/login', [AdminLoginController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout')->middleware('auth:admin');

// Admin Routes (Protected)
Route::middleware('auth:admin')->prefix('admin')->group(function () {
    Route::get('/ads', [AdAdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/ads/{id}', [AdAdminController::class, 'show'])->name('admin.ads.show');
    Route::post('/ads/{id}/approve', [AdAdminController::class, 'approve']);
    Route::post('/ads/{id}/reject', [AdAdminController::class, 'reject']);
    Route::post('/ads/{id}/update-range', [AdAdminController::class, 'updateRange']);
    Route::post('/ads/{id}/update-all', [AdAdminController::class, 'updateAll']);
    Route::post('/ads/{id}/update', [AdAdminController::class, 'updateField']);
});

Route::post('/ads/{id}/update-range', [AdAdminController::class, 'updateRange']);