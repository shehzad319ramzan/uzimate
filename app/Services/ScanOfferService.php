<?php

namespace App\Services;

use App\Models\CustomerLog;
use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScanOfferService
{
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
        return CustomerLog::where('user_id', $userId)
            ->where('action_type', 'qr_code_scanned')
            ->where('related_model_type', Offer::class)
            ->where('related_model_id', $offerId)
            ->exists();
    }

    /**
     * Create scan log and credit points.
     */
    public function createScanLog(Offer $offer, object $user, Request $request): void
    {
        $pointsEarned = (int) $offer->points_required;

        CustomerLog::create([
            'merchant_id' => $offer->merchant_id,
            'site_id' => $offer->site_id,
            'user_id' => $user->id,
            'action_type' => 'qr_code_scanned',
            'action_category' => 'scans',
            'description' => "Scanned offer: {$offer->title} - earned {$pointsEarned} points",
            'points_affected' => $pointsEarned,
            'related_model_type' => Offer::class,
            'related_model_id' => $offer->id,
            'performed_by_id' => $user->id,
            'metadata' => [
                'offer_title' => $offer->title,
                'points_earned' => $pointsEarned,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Get user's current points balance.
     */
    public function getUserPointsBalance(string $userId): int
    {
        return max(0, (int) CustomerLog::where('user_id', $userId)
            ->whereNotNull('points_affected')
            ->sum('points_affected'));
    }
}
