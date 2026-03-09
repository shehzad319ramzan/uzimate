<?php

namespace App\Observers;

use App\Models\CustomerLog;
use App\Models\MerchantScan;
use Illuminate\Support\Facades\Log;

class MerchantScanObserver
{
    public function created(MerchantScan $merchantScan): void
    {
        try {
            $pointsEarned = (int) ($merchantScan->points_earned ?? 0);
            $merchantName = $merchantScan->merchant?->name ?? 'Merchant';

            $description = $pointsEarned > 0
                ? "Scanned merchant QR: {$merchantName} - earned {$pointsEarned} points"
                : "Scanned merchant QR: {$merchantName}";

            CustomerLog::create([
                'merchant_id' => $merchantScan->merchant_id,
                'site_id' => null,
                'offer_id' => null,
                'user_id' => $merchantScan->user_id,
                'action_type' => 'merchant_qr_scanned',
                'action_category' => 'scans',
                'description' => $description,
                'points_affected' => $pointsEarned,
                'related_model_type' => MerchantScan::class,
                'related_model_id' => $merchantScan->id,
                'performed_by_id' => $merchantScan->user_id,
                'metadata' => array_merge($merchantScan->metadata ?? [], [
                    'merchant_name' => $merchantName,
                    'points_earned' => $pointsEarned,
                ]),
                'ip_address' => $merchantScan->ip_address ?? request()->ip(),
                'user_agent' => $merchantScan->user_agent ?? request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create customer log for merchant scan: ' . $e->getMessage());
        }
    }
}
