<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
});

Route::get('/terms-and-conditions', function () {
    return view('terms-and-conditions');
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
        Route::get('plans/create', [AdminAuthController::class, 'createPlan'])->name('plans.create');
        Route::post('plans', [AdminAuthController::class, 'storePlan'])->name('plans.store');
        Route::get('plans/{plan}/edit', [AdminAuthController::class, 'editPlan'])->name('plans.edit');
        Route::put('plans/{plan}', [AdminAuthController::class, 'updatePlan'])->name('plans.update');
        Route::get('referrals', [AdminAuthController::class, 'referrals'])->name('referrals');

        Route::get('batch-status', [AdminAuthController::class, 'batchStatus'])->name('batch.status');
        Route::get('ads/{type}/{id}', [AdminAuthController::class, 'showAd'])->name('ad.show');
        Route::post('ads/{type}/{id}/status', [AdminAuthController::class, 'updateAdStatus'])->name('ad.status');
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
    });
});
