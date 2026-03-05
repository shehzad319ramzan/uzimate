<?php

namespace Database\Seeders;

use App\Constants\Constants;
use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SettingSeeder extends Seeder
{
    /**
     * Copy default logo.webp to storage and return the path for DB.
     */
    private function getDefaultLogoPath(): string
    {
        $sourcePath = public_path('dashboard/images/default/logo.webp');
        $storagePath = 'settings/logo.webp';

        if (File::exists($sourcePath)) {
            Storage::disk('public')->put($storagePath, File::get($sourcePath));
        }

        return $storagePath;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = new Setting();

        $setting->name = env('APP_NAME', 'Laravel');
        $setting->url = env('APP_URL', 'http://localhost');
        $setting->email = env('MAIL_FROM_ADDRESS', 'admin@example.com');

        $setting->smtp_host = env('MAIL_HOST', '127.0.0.1');
        $setting->smtp_port = env('MAIL_PORT', 2525);
        $setting->smtp_username = env('MAIL_USERNAME', '');
        $setting->smtp_password = env('MAIL_PASSWORD', '');
        $setting->smtp_email = env('MAIL_FROM_ADDRESS', 'admin@example.com');
        $setting->smtp_sender_name = env('APP_NAME', 'Laravel');
        $setting->smtp_encryption = 'tls';

        $setting->firebase_project_id = env('FIREBASE_PROJECT_ID', 'uzimate-c4729');
        $credentialsPath = env('FIREBASE_CREDENTIALS_FILE', 'storage/firebase-credentials.json');
        $fullPath = str_starts_with($credentialsPath, '/') || (strlen($credentialsPath) >= 2 && $credentialsPath[1] === ':')
            ? $credentialsPath
            : base_path($credentialsPath);
        $setting->firebase_credentials = File::isFile($fullPath) ? File::get($fullPath) : null;

        $setting->spin_spins_per_day = 1;
        $setting->spin_outcome_nothing = 50;
        $setting->spin_outcome_points = 50;
        $setting->spin_outcome_offer = 0;
        $setting->spin_outcome_discount = 0;
        $setting->spin_points_min = 10;
        $setting->spin_points_max = 90;
        $setting->spin_discount_min = 0;
        $setting->spin_discount_max = 0;

        $setting->save();

        $setting->file()->create([
            'name' => 'logo.webp',
            'path' => $this->getDefaultLogoPath(),
            'type' => Constants::LOGOTYPE,
        ]);
    }
}

