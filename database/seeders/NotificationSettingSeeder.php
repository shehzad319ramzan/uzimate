<?php

namespace Database\Seeders;

use App\Models\NotificationSetting;
use Illuminate\Database\Seeder;

class NotificationSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'type' => NotificationSetting::TYPE_MISS_YOU,
                'name' => 'Miss You',
                'config' => [
                    'inactive_days' => 7,
                    'message_template' => 'We miss you, {{name}}! Come back and check our latest updates.',
                    'channels' => ['email', 'push'],
                ],
                'is_active' => true,
            ],
            [
                'type' => NotificationSetting::TYPE_SPECIAL_DAY,
                'name' => 'Special Day',
                'config' => [
                    'message_template' => '',
                    'channels' => ['email', 'push'],
                ],
                'is_active' => true,
            ],
            [
                'type' => NotificationSetting::TYPE_SPECIAL_OFFER,
                'name' => 'Special Offer',
                'config' => [
                    'message_template' => 'New offer: {{offer_title}}! Get great rewards.',
                    'channels' => ['email', 'push'],
                ],
                'is_active' => true,
            ],
            [
                'type' => NotificationSetting::TYPE_BIRTHDAY,
                'name' => 'Birthday',
                'config' => [
                    'message_template' => 'Happy Birthday, {{name}}! You\'ve earned {{points}} points.',
                    'reward_points' => 5,
                    'channels' => ['email', 'push'],
                ],
                'is_active' => true,
            ],
        ];

        foreach ($defaults as $row) {
            NotificationSetting::updateOrCreate(
                ['type' => $row['type']],
                $row
            );
        }
    }
}
