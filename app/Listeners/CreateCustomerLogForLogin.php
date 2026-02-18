<?php

namespace App\Listeners;

use App\Events\CustomerLoggedIn;
use App\Models\CustomerLog;
use App\Services\RewardRuleService;

class CreateCustomerLogForLogin
{
    public function __construct(protected RewardRuleService $rewardRuleService)
    {
    }

    /**
     * Handle the event. Always create customer log; points only when reward rule is active (and first_time_only respected).
     */
    public function handle(CustomerLoggedIn $event): void
    {
        $points = 0;
        if ($this->rewardRuleService->shouldAwardPointsForAction('login', null)) {
            $points = (int) ($this->rewardRuleService->getPointsForAction('login', null) ?? 0);
            if ($points > 0 && $this->rewardRuleService->isFirstTimeOnly('login', null)) {
                $alreadyReceivedLoginPoints = CustomerLog::where('user_id', $event->userId)
                    ->where('action_type', 'login')
                    ->whereNotNull('points_affected')
                    ->where('points_affected', '>', 0)
                    ->exists();
                if ($alreadyReceivedLoginPoints) {
                    $points = 0;
                }
            }
        }

        CustomerLog::create([
            'user_id' => $event->userId,
            'action_type' => 'login',
            'action_category' => 'system',
            'description' => $points > 0 ? "{$event->description} - earned {$points} points" : $event->description,
            'points_affected' => $points > 0 ? $points : null,
            'ip_address' => $event->request->ip(),
            'user_agent' => $event->request->userAgent(),
        ]);
    }
}
