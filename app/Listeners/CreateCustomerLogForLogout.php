<?php

namespace App\Listeners;

use App\Events\CustomerLoggedOut;
use App\Models\CustomerLog;
use App\Services\RewardRuleService;

class CreateCustomerLogForLogout
{
    public function __construct(protected RewardRuleService $rewardRuleService)
    {
    }

    /**
     * Create customer log for logout only when reward rule for logout is active.
     */
    public function handle(CustomerLoggedOut $event): void
    {
        if (!$this->rewardRuleService->shouldAwardPointsForAction('logout', null)) {
            return;
        }
        $points = (int) ($this->rewardRuleService->getPointsForAction('logout', null) ?? 0);
        CustomerLog::create([
            'user_id' => $event->userId,
            'action_type' => 'logout',
            'action_category' => 'system',
            'description' => $points > 0 ? "{$event->description} - earned {$points} points" : $event->description,
            'points_affected' => $points > 0 ? $points : null,
            'ip_address' => $event->request->ip(),
            'user_agent' => $event->request->userAgent(),
        ]);
    }
}
