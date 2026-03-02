<?php

namespace App\Services;

use App\Models\CustomerLog;
use App\Models\Offer;
use App\Models\OfferScan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScanOfferService
{
    public function __construct(
        protected RewardRuleService $rewardRuleService
    ) {}

    /**
     * Find and return a valid (active) offer by ID.
     */
    public function findValidOffer(string $offerId): ?Offer
    {
        return Offer::with('merchant', 'site')
            ->where('id', $offerId)
            ->where(function ($q) {
                $q->where('status', '1')->orWhere('status', 1);
            })
            ->first();
    }

    /**
     * Validate offer expiry date. Returns [valid, message].
     */
    public function validateExpiry(Offer $offer): array
    {
        if (!$offer->expires_on || !$offer->expires_on->isPast()) {
            return [true, null];
        }
        return [false, 'This offer has expired'];
    }

    /**
     * Validate offer weekday restrictions. Returns [valid, message].
     */
    public function validateWeekdays(Offer $offer): array
    {
        $weekdays = $offer->weekdays;
        if (empty($weekdays) || !is_array($weekdays)) {
            return [true, null];
        }

        $currentDay = Carbon::now()->format('D');
        if (in_array($currentDay, $weekdays, true)) {
            return [true, null];
        }

        return [false, 'This offer is not valid today. Valid days: ' . implode(', ', $weekdays)];
    }

    /**
     * Check if user has already scanned this offer.
     */
    public function hasUserScannedOffer(string $userId, string $offerId): bool
    {
        return OfferScan::where('user_id', $userId)
            ->where('offer_id', $offerId)
            ->exists();
    }

    /**
     * Create scan log and credit points.
     * When reward rule for qr_code_scanned is inactive: still create log but with 0 points (audit trail, no reward).
     * @return int Points actually awarded (0 when rule inactive).
     */
    public function createScanLog(Offer $offer, object $user, Request $request): int
    {
        $pointsEarned = 0;
        if ($this->rewardRuleService->shouldAwardPointsForAction('qr_code_scanned', $offer->merchant_id)) {
            $pointsEarned = (int) $offer->points_required;
        }

        OfferScan::create([
            'merchant_id' => $offer->merchant_id,
            'site_id' => $offer->site_id,
            'offer_id' => $offer->id,
            'user_id' => $user->id,
            'points_earned' => $pointsEarned,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'metadata' => [
                'offer_title' => $offer->title,
                'points_earned' => $pointsEarned,
            ],
        ]);

        return $pointsEarned;
    }

    /**
     * Get user's current points balance (all merchants).
     */
    public function getUserPointsBalance(string $userId): int
    {
        return max(0, (int) CustomerLog::where('user_id', $userId)
            ->whereNotNull('points_affected')
            ->sum('points_affected'));
    }

    /**
     * Get user's points balance for a specific merchant (all action types).
     */
    public function getUserPointsBalanceByMerchant(string $userId, string $merchantId): int
    {
        return max(0, (int) CustomerLog::where('user_id', $userId)
            ->where('merchant_id', $merchantId)
            ->whereNotNull('points_affected')
            ->sum('points_affected'));
    }

    /**
     * Get user's points from offer scans (QR Code Scanned) for a specific merchant only.
     */
    public function getUserPointsFromOfferScansByMerchant(string $userId, string $merchantId): int
    {
        return max(0, (int) CustomerLog::where('user_id', $userId)
            ->where('merchant_id', $merchantId)
            ->where('action_type', 'qr_code_scanned')
            ->whereNotNull('points_affected')
            ->sum('points_affected'));
    }

    /**
     * Get user's points balance excluding given action types (e.g. qr_code_scanned, survey_completed).
     * When "Use Other Merchant Points = No": only this balance is allowed for that merchant's voucher.
     */
    public function getUserPointsBalanceExcludingActionTypes(string $userId, array $excludeActionTypes): int
    {
        $query = CustomerLog::where('user_id', $userId)->whereNotNull('points_affected');
        if (!empty($excludeActionTypes)) {
            $query->whereNotIn('action_type', $excludeActionTypes);
        }
        return max(0, (int) $query->sum('points_affected'));
    }


    public function getRewardHistory(string $userId, int $perPage = 20)
    {
        return CustomerLog::where('user_id', $userId)
            ->whereNotNull('points_affected')
            ->with(['relatedModel', 'offer', 'merchant', 'site'])
            ->latest()
            ->paginate($perPage);
    }
}
