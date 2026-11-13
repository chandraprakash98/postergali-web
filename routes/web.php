<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ContactUsController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/contact-us', [ContactUsController::class, 'store'])->name('contact-us.store');

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
        Route::get('influencers', [AdminAuthController::class, 'influencers'])->name('influencers');
        Route::get('influencers/create', [AdminAuthController::class, 'createInfluencer'])->name('influencers.create');
        Route::post('influencers', [AdminAuthController::class, 'storeInfluencer'])->name('influencers.store');
        Route::get('influencers/{bonusCoupon}/edit', [AdminAuthController::class, 'editInfluencer'])->name('influencers.edit');
        Route::put('influencers/{bonusCoupon}', [AdminAuthController::class, 'updateInfluencer'])->name('influencers.update');
        Route::delete('influencers/{bonusCoupon}', [AdminAuthController::class, 'deleteInfluencer'])->name('influencers.delete');
        Route::get('referrals', [AdminAuthController::class, 'referrals'])->name('referrals');
        Route::get('ugc-reports', [AdminAuthController::class, 'ugcReports'])->name('ugcReports');
        Route::get('credit-settings', [AdminAuthController::class, 'creditSettings'])->name('creditSettings');
        Route::post('credit-settings', [AdminAuthController::class, 'updateCreditSettings'])->name('creditSettings.update');
        Route::get('poster-limit-settings', [AdminAuthController::class, 'posterLimitSettings'])->name('posterLimitSettings');
        Route::post('poster-limit-settings', [AdminAuthController::class, 'updatePosterLimitSettings'])->name('posterLimitSettings.update');

        Route::get('batch-status', [AdminAuthController::class, 'batchStatus'])->name('batch.status');
        Route::get('ads/{type}/{id}', [AdminAuthController::class, 'showAd'])->name('ad.show');
        Route::post('ads/{type}/{id}/status', [AdminAuthController::class, 'updateAdStatus'])->name('ad.status');
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
    });
});
