<?php

namespace App\Observers;

use App\Models\CustomerLog;
use App\Models\PointAward;
use App\Services\RewardRuleService;
use Illuminate\Support\Facades\Log;

class PointAwardObserver
{
    public function __construct(protected RewardRuleService $rewardRuleService)
    {
    }

    /**
     * Handle the PointAward "created" event.
     * Always create customer log; points only when reward rule for point_earned is active.
     */
    public function created(PointAward $pointAward): void
    {
        try {
            $points = 0;
            if ($this->rewardRuleService->shouldAwardPointsForAction('point_earned', $pointAward->merchant_id)) {
                $points = (int) $pointAward->points_earned;
            }

            CustomerLog::create([
                'merchant_id' => $pointAward->merchant_id,
                'site_id' => $pointAward->site_id,
                'user_id' => $pointAward->user_id,
                'action_type' => 'point_earned',
                'action_category' => 'points',
                'description' => $points > 0
                    ? "Earned {$points} points from Point Award"
                    : "Point Award recorded",
                'points_affected' => $points > 0 ? $points : null,
                'related_model_type' => PointAward::class,
                'related_model_id' => $pointAward->id,
                'performed_by_id' => $pointAward->awarded_by_id ?? auth()->id(),
                'metadata' => [
                    'notes' => $pointAward->notes,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create customer log for point award: ' . $e->getMessage());
        }
    }
}

