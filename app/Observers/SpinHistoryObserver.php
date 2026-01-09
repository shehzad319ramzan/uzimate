<?php

namespace App\Observers;

use App\Models\CustomerLog;
use App\Models\SpinHistory;
use Illuminate\Support\Facades\Log;

class SpinHistoryObserver
{
    /**
     * Handle the SpinHistory "created" event.
     * Automatically creates a customer log when a spin is completed.
     */
    public function created(SpinHistory $spinHistory): void
    {
        try {
            // Determine description based on result type
            $description = match($spinHistory->spin_result_type) {
                'points' => "Won {$spinHistory->points_earned} points from spin wheel (Spin #{$spinHistory->spin_number})",
                'offer' => "Won offer from spin wheel (Spin #{$spinHistory->spin_number})",
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
                'points_affected' => $spinHistory->points_earned ?? 0,
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

