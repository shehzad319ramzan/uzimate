<?php

namespace App\Providers;

use App\Constants\Constants;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Log;
use App\Helper\Helpers;
use App\Interface\UserInterface;
use App\Models\PointAward;
use App\Models\SpinHistory;
use App\Observers\PointAwardObserver;
use App\Observers\SpinHistoryObserver;
use App\Repositories\UserRepository;
use App\Services\SettingService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use App\Events\CustomerLoggedIn;
use App\Events\CustomerLoggedOut;
use App\Events\OfferCreated;
use App\Listeners\CreateCustomerLogForLogin;
use App\Listeners\SendOfferNotificationListener;
use App\Listeners\CreateCustomerLogForLogout;
use App\Models\MerchantScan;
use App\Models\OfferScan;
use App\Observers\MerchantScanObserver;
use App\Observers\OfferScanObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserInterface::class, UserRepository::class);
    }


    public function boot(SettingService $settingsService): void
    {
        try {
            if (!Helpers::dbConnectionStatus()) {
                Log::error('Database connection failed.');
                abort(500, 'Database connection failed.');
            }

            $this->forceSchemeHttps();
            Gate::before(function ($user, $ability) {
                return $user->hasRole(Constants::SUPERADMIN, 'web') ? true : null;
            });

            $this->registerBladeComponents();
            $this->registerObservers();

            Route::middleware(['web', 'auth', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath'])
                ->prefix(LaravelLocalization::setLocale() . '/my-account')
                ->group(base_path('routes/panel.php'));

            if (Schema::hasTable('cache') && Schema::hasTable('settings')) {
                $setting = $settingsService->getSettings();
                $settingsService->applySettings();
                $this->setLocale($setting->default_language ?? config('app.locale'));
                View::share('setting', $setting);
            }
        } catch (\Exception $e) {
            Log::error('Error in AppServiceProvider: ' . $e->getMessage());
            abort(500, 'Application boot failed.');
        }
    }

    private function forceSchemeHttps(): void
    {
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
    }

    private function setLocale($lang): void
    {
        app()->setLocale($lang);
    }

    private function registerObservers(): void
    {
        if (!Schema::hasTable('customer_logs')) {
            return;
        }
        PointAward::observe(PointAwardObserver::class);
        SpinHistory::observe(SpinHistoryObserver::class);
        if (Schema::hasTable('offer_scans')) {
            OfferScan::observe(OfferScanObserver::class);
        }
        if (Schema::hasTable('merchant_scans')) {
            MerchantScan::observe(MerchantScanObserver::class);
        }
        Event::listen(CustomerLoggedIn::class, CreateCustomerLogForLogin::class);
        Event::listen(CustomerLoggedOut::class, CreateCustomerLogForLogout::class);
        Event::listen(OfferCreated::class, SendOfferNotificationListener::class);
    }

    private function registerBladeComponents(): void
    {
        Blade::component('auth.pages.users.profile.layout', 'my-profile');
        Blade::component('auth.pages.settings.layout', 'settings');
        Blade::component('auth.pages.notifications.layout', 'notification-management');
    }
}
