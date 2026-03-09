<?php

namespace Database\Seeders;

use App\Models\RewardRule;
use Illuminate\Database\Seeder;

class RewardRuleSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = RewardRule::defaultActionTypes();

        foreach ($defaults as $actionType => $label) {
            RewardRule::updateOrCreate(
                [
                    'action_type' => $actionType,
                    'merchant_id' => null,
                ],
                [
                    'label' => $label,
                    'points' => match ($actionType) {
                        'login' => 80,
                        'referral_completed' => 5000,
                        'merchant_qr_scanned' => 10,
                        default => null,
                    },
                    'trigger_condition' => $actionType === 'login' ? RewardRule::TRIGGER_FIRST_TIME_ONLY : RewardRule::TRIGGER_EVERY_TIME,
                    'is_active' => true,
                ]
            );
        }
    }
}
