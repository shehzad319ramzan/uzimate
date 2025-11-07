<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    # Looking to send emails in production? Check out our Email API/SMTP product!

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

        $setting->save();

        $setting->file()->create(['name' => 'logo.webp', 'path' => 'settings/logo.webp', 'type' => 'logo']);
    }
}
