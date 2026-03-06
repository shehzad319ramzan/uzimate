<?php

use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CustomerLogController;
use App\Http\Controllers\CustomerScansController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\InviteFriendController;
use App\Http\Controllers\MerchantCategoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PointAwardController;
use App\Http\Controllers\RewardRuleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SiteUserController;
use App\Http\Controllers\SpinHistoryController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\SurveyResponseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
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
        Route::prefix('merchant-categories')->as('merchantcategories.')->controller(MerchantCategoryController::class)->group(function () {
            Route::get('create', 'create')->name('create');
            Route::get('', 'index')->name('index');
            Route::post('store', 'store')->name('store');
            Route::put('update/{id}', 'update')->name('update');
            Route::delete('delete/{id}', 'destroy')->name('destroy');
            Route::get('detail/{id}', 'show')->name('show');
            Route::get('edit/{id}', 'edit')->name('edit');
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

        Route::prefix('offers')->as('offers.')->controller(OfferController::class)->group(function () {
            Route::get('create', 'create')->name('create');
            Route::get('', 'index')->name('index');
            Route::post('store', 'store')->name('store');
            Route::put('update/{id}', 'update')->name('update');
            Route::delete('delete/{id}', 'destroy')->name('destroy');
            Route::get('detail/{id}', 'show')->name('show');
            Route::get('edit/{id}', 'edit')->name('edit');
        });
        Route::prefix('points-award')->as('pointawards.')->controller(PointAwardController::class)->group(function () {
            Route::get('create', 'create')->name('create');
            Route::get('', 'index')->name('index');
            Route::post('store', 'store')->name('store');
            Route::put('update/{id}', 'update')->name('update');
            Route::delete('delete/{id}', 'destroy')->name('destroy');
            Route::get('detail/{id}', 'show')->name('show');
            Route::get('edit/{id}', 'edit')->name('edit');
        });
        Route::prefix('spin-history')->as('spinhistories.')->controller(SpinHistoryController::class)->group(function () {
            Route::get('create', 'create')->name('create');
            Route::get('', 'index')->name('index');
            Route::post('store', 'store')->name('store');
            Route::put('update/{id}', 'update')->name('update');
            Route::delete('delete/{id}', 'destroy')->name('destroy');
            Route::get('detail/{id}', 'show')->name('show');
            Route::get('edit/{id}', 'edit')->name('edit');
        });

        Route::prefix('surveys')->as('surveys.')->controller(SurveyController::class)->group(function () {
            Route::get('', 'index')->name('index')->middleware('can:view_survey');
            Route::get('create', 'create')->name('create')->middleware('can:add_survey');
            Route::post('store', 'store')->name('store')->middleware('can:add_survey');
            Route::get('detail/{id}', 'show')->name('show')->middleware('can:view_survey');
            Route::get('edit/{id}', 'edit')->name('edit')->middleware('can:edit_survey');
            Route::put('update/{id}', 'update')->name('update')->middleware('can:edit_survey');
            Route::delete('delete/{id}', 'destroy')->name('destroy')->middleware('can:delete_survey');
            Route::post('{id}/questions', 'storeQuestion')->name('questions.store')->middleware('can:edit_survey');
            Route::delete('questions/{questionId}', 'destroyQuestion')->name('questions.destroy')->middleware('can:edit_survey');
            Route::get('{id}/questions-preview', 'questionsPreview')->name('questions.preview')->middleware('can:view_survey');
        });

        Route::prefix('survey-responses')->as('surveyresponses.')->controller(SurveyResponseController::class)->middleware('can:view_survey')->group(function () {
            Route::get('', 'index')->name('index');
            Route::get('detail/{id}', 'show')->name('show');
        });

        Route::prefix('invite-friends')->as('invitefriends.')->controller(InviteFriendController::class)->middleware('can:view_invite_friend')->group(function () {
            Route::get('', 'index')->name('index');
            Route::get('detail/{id}', 'show')->name('show');
            Route::delete('delete/{id}', 'destroy')->name('destroy')->middleware('can:delete_invite_friend');
        });

        Route::prefix('vouchers')->as('vouchers.')->controller(VoucherController::class)->middleware('can:view_voucher')->group(function () {
            Route::get('', 'index')->name('index');
            Route::get('create', 'create')->name('create')->middleware('can:add_voucher');
            Route::post('store', 'store')->name('store')->middleware('can:add_voucher');
            Route::get('offers-by-merchant/{merchantId}', 'offersByMerchant')->name('offers-by-merchant')->middleware('can:add_voucher');
            Route::get('{id}/preview', 'preview')->name('preview');
            Route::get('detail/{id}', 'show')->name('show');
            Route::get('edit/{id}', 'edit')->name('edit')->middleware('can:edit_voucher');
            Route::put('update/{id}', 'update')->name('update')->middleware('can:edit_voucher');
            Route::delete('delete/{id}', 'destroy')->name('destroy')->middleware('can:delete_voucher');
        });

        Route::prefix('reward-rules')->as('reward-rules.')->controller(RewardRuleController::class)->group(function () {
            Route::get('create', 'create')->name('create');
            Route::get('', 'index')->name('index');
            Route::post('store', 'store')->name('store');
            Route::put('update/{id}', 'update')->name('update');
            Route::delete('delete/{id}', 'destroy')->name('destroy');
            Route::get('detail/{id}', 'show')->name('show');
            Route::get('edit/{id}', 'edit')->name('edit');
        });

        Route::prefix('notifications')->as('notifications.')->middleware('can:view_notification')->controller(NotificationController::class)->group(function () {
            Route::get('api/inactive-customers-preview', 'getInactiveCustomersPreview')->name('api.inactive-customers-preview');
            Route::get('api/inactive-customers', 'getInactiveCustomers')->name('api.inactive-customers');
            Route::get('api/birthday-preview', 'getBirthdayPreview')->name('api.birthday-preview');
            Route::get('api/birthday-counts', 'getBirthdayCounts')->name('api.birthday-counts');
            Route::get('api/customers', 'getCustomers')->name('api.customers');
            Route::post('update-settings/{type}', 'updateSettings')->name('update-settings')->middleware('can:edit_notification');
            Route::post('send-miss-you', 'sendMissYou')->name('send-miss-you')->middleware('can:add_notification');
            Route::post('send-special-day', 'sendSpecialDay')->name('send-special-day')->middleware('can:add_notification');
            Route::post('send-special-offer', 'sendSpecialOffer')->name('send-special-offer')->middleware('can:add_notification');
            Route::post('send-birthday', 'sendBirthday')->name('send-birthday')->middleware('can:add_notification');
            Route::get('{blade}', 'index')->name('index');
        });

        Route::prefix('customer-logs')->as('customerlogs.')->controller(CustomerLogController::class)->group(function () {
            Route::get('', 'index')->name('index');
            Route::get('detail/{id}', 'show')->name('show');
        });

        Route::prefix('customer-scans')->as('customer-scans.')->controller(CustomerScansController::class)->middleware('permission:view_customer_scan|view_offer_scan')->group(function () {
            Route::get('', 'index')->name('index');
        });

        Route::prefix('inbox')->as('inbox.')->controller(InboxController::class)->group(function () {
            Route::get('create', 'create')->name('create');
            Route::get('', 'index')->name('index');
            Route::post('store', 'store')->name('store');
            Route::put('update/{id}', 'update')->name('update');
            Route::delete('delete/{id}', 'destroy')->name('destroy');
            Route::get('detail/{id}', 'show')->name('show');
            Route::get('edit/{id}', 'edit')->name('edit');
        });

        Route::prefix('feedback')->as('feedbacks.')->controller(FeedbackController::class)->group(function () {
            Route::get('create', 'create')->name('create');
            Route::get('', 'index')->name('index');
            Route::post('store', 'store')->name('store');
            Route::put('update/{id}', 'update')->name('update');
            Route::delete('delete/{id}', 'destroy')->name('destroy');
            Route::get('detail/{id}', 'show')->name('show');
            Route::get('edit/{id}', 'edit')->name('edit');
        });

        Route::prefix('s')->as('settings.')->middleware('can:site_setting')->controller(SettingController::class)->group(function () {

            Route::get('{blade}', 'index')->name('index');

            Route::put('basic_info/update', 'basic_info')->name('basic_info');
            Route::put('smtp/update', 'smtp_update')->name('smtp_update');
            Route::put('firebase/update', 'firebase_update')->name('firebase_update');
            Route::put('spin/update', 'spin_update')->name('spin_update');
            Route::put('social-logins/update', 'social_logins_update')->name('social_logins_update');
            Route::put('registration/update', 'registeration_update')->name('registeration_update');
            Route::post('clear-cache', 'clear_cache')->name('clear_cache');

            Route::post('update/default-language', 'update_default_language')->name('update_default_language');
            Route::post('install/language', 'install_language')->name('install_language');
            Route::post('active/language', 'active_language')->name('active_language');
        });
    }
);
