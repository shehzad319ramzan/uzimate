<?php

namespace App\Services;

use App\Constants\CacheConstants;
use Illuminate\Support\Facades\Cache;
use App\Repositories\SettingRepository;

class SettingService
{
    protected $settingRepo;

    public function __construct(SettingRepository $settingRepo)
    {
        $this->settingRepo = $settingRepo;
    }

    public function getSettings()
    {
        return Cache::remember(CacheConstants::SITE_SETTINGS, 3600, function () {
            return $this->settingRepo->index();
        });
    }

    public function clearCache()
    {
        Cache::forget(CacheConstants::SITE_SETTINGS);
    }

    public function applySettings()
    {
        $setting = $this->getSettings();

        if (!$setting) {
            return;
        }

        config([
            'app.name' => $setting->name,
            'app.url' => $setting->url,
            'app.logo' => $setting->logo(),

            'mail.mailers.smtp.host' => $setting->smtp_host ?? env('MAIL_HOST'),
            'mail.mailers.smtp.port' => (int)($setting->smtp_port ?? env('MAIL_PORT')),
            'mail.mailers.smtp.username' => $setting->smtp_username ?? env('MAIL_USERNAME'),
            'mail.mailers.smtp.password' => $setting->smtp_password ?? env('MAIL_PASSWORD'),
            'mail.mailers.smtp.encryption' => $setting->smtp_encryption ?? env('MAIL_ENCRYPTION'),
            'mail.from.address' => $setting->smtp_email ?? env('MAIL_FROM_ADDRESS'),
            'mail.from.name' => $setting->smtp_sender_name ?? env('MAIL_FROM_NAME'),
        ]);

        config([
            'spin.spins_per_day' => (int) ($setting->spin_spins_per_day ?? config('spin.spins_per_day', 1)),
            'spin.default_site_id' => $setting->spin_default_site_id ?? config('spin.default_site_id'),
            'spin.outcomes' => [
                'nothing' => (int) ($setting->spin_outcome_nothing ?? config('spin.outcomes.nothing', 50)),
                'points' => (int) ($setting->spin_outcome_points ?? config('spin.outcomes.points', 30)),
                'offer' => (int) ($setting->spin_outcome_offer ?? config('spin.outcomes.offer', 15)),
                'discount' => (int) ($setting->spin_outcome_discount ?? config('spin.outcomes.discount', 5)),
            ],
            'spin.points_range' => [
                (int) ($setting->spin_points_min ?? config('spin.points_range.0', 25)),
                (int) ($setting->spin_points_max ?? config('spin.points_range.1', 100)),
            ],
            'spin.discount_range' => [
                (int) ($setting->spin_discount_min ?? config('spin.discount_range.0', 5)),
                (int) ($setting->spin_discount_max ?? config('spin.discount_range.1', 20)),
            ],
        ]);
    }
}
