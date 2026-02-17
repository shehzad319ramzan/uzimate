<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\MerchantApiController;
use App\Http\Controllers\Api\OfferApiController;
use App\Http\Controllers\Api\PasswordApiController;
use Illuminate\Support\Facades\Route;

// Public routes
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
    Route::post('edit-profile', [UserController::class, 'edit_profile']);
    Route::post('password-change', [PasswordApiController::class, 'change_password']);
});
