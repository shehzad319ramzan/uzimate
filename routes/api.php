<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\MerchantApiController;
use App\Http\Controllers\Api\OfferApiController;
use App\Http\Controllers\Api\PasswordApiController;
use App\Http\Controllers\Api\RewardsApiController;
use App\Http\Controllers\Api\ScanOfferApiController;
use App\Http\Controllers\Api\SpinApiController;
use App\Http\Controllers\Api\InviteFriendApiController;
use App\Http\Controllers\Api\SurveyApiController;
use App\Http\Controllers\Api\VoucherApiController;
use Illuminate\Support\Facades\Route;

// Merchant routes
Route::get('merchant-categories', [MerchantApiController::class, 'merchantCategories']);
Route::get('merchants', [MerchantApiController::class, 'merchants']);
Route::get('merchants/{id}', [MerchantApiController::class, 'merchantDetail']);
Route::get('offers', [OfferApiController::class, 'index']);
Route::get('offers/{id}', [OfferApiController::class, 'show']);
Route::post('login', [LoginController::class, 'loginWithApi']);
Route::post('register', [RegisterController::class, 'registerApi']);

Route::post('get-email', [PasswordApiController::class, 'get_email']);
Route::post('verification-code', [PasswordApiController::class, 'verification_code'])->name('verification_code');
Route::post('reset-password', [PasswordApiController::class, 'reset_password'])->name('reset_password');

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [HomeController::class, 'logoutApi']);
    Route::get('profile', [UserController::class, 'profileApi']);
    Route::get('my-rewards', [RewardsApiController::class, 'index']);
    Route::get('my-scans', [ScanOfferApiController::class, 'index']);
    Route::post('edit-profile', [UserController::class, 'edit_profile']);
    Route::post('password-change', [PasswordApiController::class, 'change_password']);
    Route::post('scan-offer', [ScanOfferApiController::class, 'scan']);

    Route::get('referral-code', [InviteFriendApiController::class, 'referralCode']);
    Route::get('invited-friends', [InviteFriendApiController::class, 'invitedFriends']);

    Route::get('vouchers', [VoucherApiController::class, 'index']);
    Route::get('vouchers/{id}', [VoucherApiController::class, 'show']);
    Route::post('vouchers/{id}/redeem', [VoucherApiController::class, 'redeem']);

    Route::get('spin/eligibility', [SpinApiController::class, 'eligibility']);
    Route::post('spin', [SpinApiController::class, 'spin']);

    Route::get('surveys', [SurveyApiController::class, 'index']);
    Route::get('surveys/{id}', [SurveyApiController::class, 'show']);
    Route::get('surveys/{id}/form', [SurveyApiController::class, 'form']);
    Route::post('surveys/{id}/submit', [SurveyApiController::class, 'submit']);
});
