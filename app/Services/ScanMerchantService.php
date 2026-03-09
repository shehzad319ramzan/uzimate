<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\MerchantScan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScanMerchantService
{
    public function __construct(
        protected RewardRuleService $rewardRuleService
    ) {}


    public function findMerchantById(string $merchantId): ?Merchant
    {
        return Merchant::where('id', $merchantId)
            ->where(function ($q) {
                $q->where('status', '1')->orWhere('status', 1);
            })
            ->first();
    }

    public function hasUserScannedMerchantToday(string $userId, string $merchantId): bool
    {
        return MerchantScan::where('user_id', $userId)
            ->where('merchant_id', $merchantId)
            ->whereDate('created_at', Carbon::today())
            ->exists();
    }


    public function createScan(Merchant $merchant, object $user, Request $request): int
    {
        $pointsEarned = 0;
        if ($merchant->qr_scan_points !== null && (int) $merchant->qr_scan_points >= 0) {
            $pointsEarned = (int) $merchant->qr_scan_points;
        } elseif ($this->rewardRuleService->shouldAwardPointsForAction('merchant_qr_scanned', $merchant->id)) {
            $pointsEarned = (int) ($this->rewardRuleService->getPointsForAction('merchant_qr_scanned', $merchant->id) ?? 0);
        }

        MerchantScan::create([
            'merchant_id' => $merchant->id,
            'user_id' => $user->id,
            'points_earned' => $pointsEarned,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'metadata' => [
                'merchant_name' => $merchant->name,
                'points_earned' => $pointsEarned,
            ],
        ]);

        return $pointsEarned;
    }
}
