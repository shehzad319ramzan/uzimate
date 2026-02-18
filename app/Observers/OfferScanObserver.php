<?php

namespace App\Observers;

use App\Models\CustomerLog;
use App\Models\OfferScan;
use Illuminate\Support\Facades\Log;

class OfferScanObserver
{
    /**
     * Handle the OfferScan "created" event.
     * Always create customer log; points stored on OfferScan.
     */
    public function created(OfferScan $offerScan): void
    {
        try {
            $pointsEarned = (int) ($offerScan->points_earned ?? 0);
            $offerTitle = $offerScan->offer?->title ?? ($offerScan->metadata['offer_title'] ?? 'Offer');

            $description = $pointsEarned > 0
                ? "Scanned offer: {$offerTitle} - earned {$pointsEarned} points"
                : "Scanned offer: {$offerTitle}";

            CustomerLog::create([
                'merchant_id' => $offerScan->merchant_id,
                'site_id' => $offerScan->site_id,
                'offer_id' => $offerScan->offer_id,
                'user_id' => $offerScan->user_id,
                'action_type' => 'qr_code_scanned',
                'action_category' => 'scans',
                'description' => $description,
                'points_affected' => $pointsEarned,
                'related_model_type' => OfferScan::class,
                'related_model_id' => $offerScan->id,
                'performed_by_id' => $offerScan->user_id,
                'metadata' => array_merge($offerScan->metadata ?? [], [
                    'offer_title' => $offerTitle,
                    'points_earned' => $pointsEarned,
                ]),
                'ip_address' => $offerScan->ip_address ?? request()->ip(),
                'user_agent' => $offerScan->user_agent ?? request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create customer log for offer scan: ' . $e->getMessage());
        }
    }
}
