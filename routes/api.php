<?php

use App\Http\Controllers\API\AdminPackageController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CreditController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\InvoiceController;
use App\Http\Controllers\API\JobController;
use App\Http\Controllers\API\OfferController;
use App\Http\Controllers\API\OtpController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\PosterController;
use App\Http\Controllers\API\ReferralController;
use App\Http\Controllers\API\UgcReportController;
use App\Http\Controllers\AdminAuthController;
use Illuminate\Support\Facades\Route;

Route::post('ugc/report', [UgcReportController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('ugc-reports.store');

Route::prefix('v1')->group(function () {
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('posters/check', [PosterController::class, 'check']);
    Route::get('offers/search', [OfferController::class, 'search']);
    Route::apiResource('offers', OfferController::class);
    Route::get('jobs/search', [JobController::class, 'search']);
    Route::apiResource('jobs', JobController::class);
    Route::post('notifications', [AdminAuthController::class, 'storeNotification']);
    Route::post('referrals', [ReferralController::class, 'store']);
    Route::get('referrals/check', [ReferralController::class, 'check']);
    Route::get('customers/check', [CustomerController::class, 'check']);
    Route::get('customers/exists', [CustomerController::class, 'exists']);
    Route::post('auth/otp/send', [OtpController::class, 'send']);
    Route::post('auth/otp/verify', [OtpController::class, 'verify']);
    Route::get('customers/poster-ads', [CustomerController::class, 'posterAds']);
    Route::get('customers/my-posters', [CustomerController::class, 'posterAds']);
    Route::get('my-posters', [CustomerController::class, 'posterAds']);
    Route::get('customers/{customerId}/balance', [CustomerController::class, 'balance']);
    Route::match(['get', 'post'], 'customers/invoice', [InvoiceController::class, 'downloadInvoice']);
    Route::match(['get', 'post'], 'payments/invoice', [InvoiceController::class, 'downloadInvoice']);

    // Public / App Credit Package Endpoints
    Route::get('credits/packages', [CreditController::class, 'packages']);
    Route::get('credits/packages/{id}', [CreditController::class, 'package']);

    // Webhooks for payment notifications
    Route::post('credits/webhooks/razorpay', [CreditController::class, 'razorpayWebhook']);
    Route::post('credits/webhooks/apple', [CreditController::class, 'appleWebhook']);

    // Authenticated Customer Credit & Top-Up Endpoints
    Route::middleware('auth.customer')->group(function () {
        Route::post('credits/topup', [CreditController::class, 'topup']);
        Route::get('credits/balance', [CreditController::class, 'balance']);
        Route::get('credits/ledger', [CreditController::class, 'ledger']);
        Route::get('credits/transactions', [CreditController::class, 'transactions']);
        Route::get('credits/transactions/{uuid}', [CreditController::class, 'transaction']);
    });

    // Authenticated Admin Package & Ledger Endpoints
    Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
        Route::get('packages', [AdminPackageController::class, 'index']);
        Route::get('packages/{id}', [AdminPackageController::class, 'show']);
        Route::post('packages', [AdminPackageController::class, 'store']);
        Route::put('packages/{id}', [AdminPackageController::class, 'update']);
        Route::patch('packages/{id}/status', [AdminPackageController::class, 'toggleStatus']);
        Route::get('credits/transactions', [AdminPackageController::class, 'transactions']);
        Route::get('credits/ledger', [AdminPackageController::class, 'ledger']);
    });
});

Route::prefix('v1')->group(function () {
    Route::apiResource('plans', PlanController::class);
});

Route::post('/admin/login', [AdminAuthController::class, 'login']);