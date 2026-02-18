<?php

namespace App\Http\Controllers\Api;

use App\Services\ScanOfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanOfferApiController extends ApiBaseController
{
    public function __construct(
        protected ScanOfferService $scanOfferService
    ) {}

    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'offer_id' => ['required', 'string', 'exists:offers,id'],
        ]);

        $user = Auth::user();
        $offerId = $request->offer_id;

        $offer = $this->scanOfferService->findValidOffer($offerId);
        if (!$offer) {
            return $this->errorResponse('Invalid or inactive offer', 404);
        }

        [$valid, $message] = $this->scanOfferService->validateExpiry($offer);
        if (!$valid) {
            return $this->errorResponse($message, 400);
        }

        [$valid, $message] = $this->scanOfferService->validateWeekdays($offer);
        if (!$valid) {
            return $this->errorResponse($message, 400);
        }

        if ($this->scanOfferService->hasUserScannedOffer($user->id, $offer->id)) {
            return $this->errorResponse('You have already scanned this offer', 400);
        }

        $pointsEarned = $this->scanOfferService->createScanLog($offer, $user, $request);

        $newBalance = $this->scanOfferService->getUserPointsBalance($user->id);

        $message = $pointsEarned > 0
            ? "Successfully scanned! You earned {$pointsEarned} points."
            : 'Scan recorded.';

        return response()->json([
            'success' => true,
            'message' => $message,
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

    protected function errorResponse(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
