<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;

Route::get('/', function () {
    return view('welcome');
});

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login']);
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('dashboard', [AdminAuthController::class, 'dashboard'])->name('dashboard');
        Route::get('all-ads', [AdminAuthController::class, 'allAds'])->name('allAds');
        Route::get('pending-ads', [AdminAuthController::class, 'pendingAds'])->name('pendingAds');
        Route::get('live-ads', [AdminAuthController::class, 'liveAds'])->name('liveAds');
        Route::get('expired-ads', [AdminAuthController::class, 'expiredAds'])->name('expiredAds');
        Route::get('pricing-info', [AdminAuthController::class, 'pricingInfo'])->name('pricingInfo');
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
    });
});
