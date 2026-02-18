<?php

namespace App\Observers;

use App\Models\CustomerLog;
use App\Models\SpinHistory;
use App\Services\RewardRuleService;
use Illuminate\Support\Facades\Log;

class SpinHistoryObserver
{
    public function __construct(protected RewardRuleService $rewardRuleService)
    {
    }

    /**
     * Handle the SpinHistory "created" event.
     * Always create customer log; points only when reward rule for spin_completed is active.
     */
    public function created(SpinHistory $spinHistory): void
    {
        try {
            $points = 0;
            if ($this->rewardRuleService->shouldAwardPointsForAction('spin_completed', $spinHistory->merchant_id)) {
                $points = (int) ($spinHistory->points_earned ?? 0);
            }

            $offerTitle = $spinHistory->offer_id ? $spinHistory->offer?->title : null;

            $description = match($spinHistory->spin_result_type) {
                'points' => $points > 0
                    ? "Won {$points} points from spin wheel (Spin #{$spinHistory->spin_number})"
                    : "Spin completed (Spin #{$spinHistory->spin_number})",
                'offer' => $offerTitle
                    ? "Won offer: {$offerTitle} from spin wheel (Spin #{$spinHistory->spin_number})"
                    : "Won offer from spin wheel (Spin #{$spinHistory->spin_number})",
                'discount' => "Won " . ($spinHistory->reward_value ? number_format($spinHistory->reward_value, 2) . '%' : '') . " discount from spin wheel (Spin #{$spinHistory->spin_number})",
                'nothing' => "Spin completed - no reward (Spin #{$spinHistory->spin_number})",
                default => "Spin completed (Spin #{$spinHistory->spin_number})",
            };

            CustomerLog::create([
                'merchant_id' => $spinHistory->merchant_id,
                'site_id' => $spinHistory->site_id,
                'user_id' => $spinHistory->user_id,
                'action_type' => 'spin_completed',
                'action_category' => 'spins',
                'description' => $description,
                'points_affected' => $points > 0 ? $points : null,
                'related_model_type' => SpinHistory::class,
                'related_model_id' => $spinHistory->id,
                'performed_by_id' => auth()->id(),
                'metadata' => [
                    'spin_result_type' => $spinHistory->spin_result_type,
                    'spin_number' => $spinHistory->spin_number,
                    'is_eligible' => $spinHistory->is_eligible,
                    'offer_id' => $spinHistory->offer_id,
                    'reward_value' => $spinHistory->reward_value,
                ],
                'ip_address' => $spinHistory->ip_address ?? request()->ip(),
                'user_agent' => request()->userAgent(),
                'location_data' => $spinHistory->device_info,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create customer log for spin history: ' . $e->getMessage());
        }
    }
}

