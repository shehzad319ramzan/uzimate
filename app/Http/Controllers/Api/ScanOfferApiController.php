<?php

namespace App\Http\Controllers\Api;

use App\Models\CustomerLog;
use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanOfferApiController extends ApiBaseController
{
    /**
     * Scan offer QR code - customer gets points according to the offer.
     */
    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'offer_id' => ['required', 'string', 'exists:offers,id'],
        ]);

        $user = Auth::user();
        $offerId = $request->offer_id;

        $offer = Offer::with('merchant', 'site')
            ->where('id', $offerId)
            ->where(function ($q) {
                $q->where('status', '1')->orWhere('status', 1);
            })
            ->first();
        if (!$offer) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive offer',
            ], 404);
        }
        if ($offer->expires_on && $offer->expires_on->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'This offer has expired',
            ], 400);
        }

        $weekdays = $offer->weekdays;
        if (!empty($weekdays) && is_array($weekdays)) {
            $currentDay = Carbon::now()->format('D');
            if (!in_array($currentDay, $weekdays, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This offer is not valid today. Valid days: ' . implode(', ', $weekdays),
                ], 400);
            }
        }

        $alreadyScanned = CustomerLog::where('user_id', $user->id)
            ->where('action_type', 'qr_code_scanned')
            ->where('related_model_type', Offer::class)
            ->where('related_model_id', $offer->id)
            ->exists();

        if ($alreadyScanned) {
            return response()->json([
                'success' => false,
                'message' => 'You have already scanned this offer',
            ], 400);
        }

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

        $newBalance = max(0, CustomerLog::where('user_id', $user->id)
            ->whereNotNull('points_affected')
            ->sum('points_affected'));

        return response()->json([
            'success' => true,
            'message' => "Successfully scanned! You earned {$pointsEarned} points.",
            'data' => [
                'offer' => [
                    'id' => $offer->id,
                    'title' => $offer->title,
                    'points_earned' => $pointsEarned,
                ],
                'points_balance' => $newBalance,
            ],
        ], 200);
    }
}
