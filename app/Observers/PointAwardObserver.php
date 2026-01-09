<?php

namespace App\Observers;

use App\Models\CustomerLog;
use App\Models\PointAward;
use Illuminate\Support\Facades\Log;

class PointAwardObserver
{
    /**
     * Handle the PointAward "created" event.
     * Automatically creates a customer log when a point award is created.
     */
    public function created(PointAward $pointAward): void
    {
        try {
            CustomerLog::create([
                'merchant_id' => $pointAward->merchant_id,
                'site_id' => $pointAward->site_id,
                'user_id' => $pointAward->user_id,
                'action_type' => 'point_earned',
                'action_category' => 'points',
                'description' => "Earned {$pointAward->points_earned} points from Point Award",
                'points_affected' => $pointAward->points_earned,
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

