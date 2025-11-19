<?php

use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SiteUserController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/logout', [HomeController::class, 'logout'])->name('logout');

Route::group(
    ['middleware' => 'checkMail'],
    function () {
        Route::get('/', [HomeController::class, 'index'])->name('auth');

        Route::get('profile', [UserController::class, 'editprofile'])->name('myprofile');
        Route::put('edit-my-profile', [UserController::class, 'updatemyprofile'])->name('updatemyprofile');

        Route::get('privacy-safety', [UserController::class, 'safety_privacy'])->name('safety_privacy');

        Route::get('password-change', [ResetPasswordController::class, 'changePassword'])->name('change_password');
        Route::post('change-password/update', [ResetPasswordController::class, 'updatePassword'])->name('update_password');

        Route::prefix('users')->as('users.')->controller(UserController::class)->middleware(['isAdmin'])->group(function () {
            Route::get('', 'index')->name('index')->middleware('can:all_user');
            Route::get('create', 'create')->name('create')->middleware('can:add_user');
            Route::get('show/{user}', 'show')->name('show')->middleware('can:view_user');
            Route::post('store', 'store')->name('store')->middleware('can:add_user');
            Route::get('edit/{user}', 'edit')->name('edit')->middleware('can:edit_user');
            Route::put('update/{user}', 'update')->name('update')->middleware('can:edit_user');
            Route::delete('delete/{user}', 'destroy')->name('destroy')->middleware('can:delete_user');
            Route::get('permissions/{user}', 'editPermissions')->name('permissions.edit')->middleware('can:assign_permission');
            Route::post('permissions/{user}', 'updatePermissions')->name('permissions.update')->middleware('can:assign_permission');
        });

        Route::prefix('roles')->as('roles.')->controller(RoleController::class)->group(function () {
            Route::get('create', 'create')->name('create')->middleware('can:add_role');
            Route::get('', 'index')->name('index')->middleware('can:all_role');
            Route::post('store', 'store')->name('store')->middleware('can:add_role');
            Route::put('update/{role}', 'update')->name('update')->middleware('can:edit_role');
            Route::delete('delete/{role}', 'destroy')->name('destroy')->middleware('can:delete_role');
            Route::get('detail/{role}', 'show')->name('show')->middleware('can:view_role');
            Route::get('edit/{role}', 'edit')->name('edit')->middleware('can:edit_role');

            Route::post('assign/permission', 'assign_permission')->name('assign_permission')->middleware('can:assign_permission');
        });
        Route::prefix('permissions')->as('permissions.')->controller(PermissionController::class)->group(function () {
            Route::get('', 'index')->name('index')->middleware('can:view_permission');
            Route::get('create', 'create')->name('create')->middleware('can:add_permission');
            Route::post('store', 'store')->name('store')->middleware('can:add_permission');
            Route::get('edit/{permission}', 'edit')->name('edit')->middleware('can:edit_permission');
            Route::put('update/{permission}', 'update')->name('update')->middleware('can:edit_permission');
            Route::delete('delete/{permission}', 'destroy')->name('destroy')->middleware('can:delete_permission');
        });
        Route::prefix('merchants')->as('merchants.')->controller(MerchantController::class)->group(function () {
            Route::get('create', 'create')->name('create');
            Route::get('', 'index')->name('index');
            Route::post('store', 'store')->name('store');
            Route::put('update/{merchant}', 'update')->name('update');
            Route::delete('delete/{merchant}', 'destroy')->name('destroy');
            Route::get('detail/{merchant}', 'show')->name('show');
            Route::get('edit/{merchant}', 'edit')->name('edit');
        });
        Route::prefix('sites')->as('sites.')->controller(SiteController::class)->group(function () {
            Route::get('create', 'create')->name('create');
            Route::get('', 'index')->name('index');
            Route::post('store', 'store')->name('store');
            Route::put('update/{site}', 'update')->name('update');
            Route::delete('delete/{site}', 'destroy')->name('destroy');
            Route::get('detail/{site}', 'show')->name('show');
            Route::get('edit/{site}', 'edit')->name('edit');
        });
        Route::prefix('siteusers')->as('siteusers.')->controller(SiteUserController::class)->group(function () {
            Route::get('create', 'create')->name('create');
            Route::get('create-super', 'createSuper')->name('create-super');
            Route::get('', 'index')->name('index');
            Route::post('store', 'store')->name('store');
            Route::post('store-super', 'storeSuper')->name('store-super');
            Route::put('update/{site}', 'update')->name('update');
            Route::delete('delete/{site}', 'destroy')->name('destroy');
            Route::get('detail/{site}', 'show')->name('show');
            Route::get('edit/{site}', 'edit')->name('edit');
        });


        Route::prefix('s')->as('settings.')->middleware('can:site_setting')->controller(SettingController::class)->group(function () {

            Route::get('{blade}', 'index')->name('index');

            Route::put('basic_info/update', 'basic_info')->name('basic_info');
            Route::put('smtp/update', 'smtp_update')->name('smtp_update');
            Route::put('social-logins/update', 'social_logins_update')->name('social_logins_update');
            Route::put('registration/update', 'registeration_update')->name('registeration_update');
            Route::post('clear-cache', 'clear_cache')->name('clear_cache');

            Route::post('update/default-language', 'update_default_language')->name('update_default_language');
            Route::post('install/language', 'install_language')->name('install_language');
            Route::post('active/language', 'active_language')->name('active_language');
        });
    }
);
