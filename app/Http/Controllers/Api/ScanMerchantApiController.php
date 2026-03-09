<?php

namespace App\Http\Controllers\Api;

use App\Services\ScanMerchantService;
use App\Services\ScanOfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanMerchantApiController extends ApiBaseController
{
    public function __construct(
        protected ScanMerchantService $scanMerchantService,
        protected ScanOfferService $scanOfferService
    ) {}

 
    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'merchant_id' => ['required', 'string', 'exists:merchants,id'],
        ]);

        $user = Auth::user();
        $merchantId = $request->input('merchant_id');

        $merchant = $this->scanMerchantService->findMerchantById($merchantId);
        if (! $merchant) {
            return $this->errorResponse('Invalid or inactive merchant QR code', 404);
        }

        if ($this->scanMerchantService->hasUserScannedMerchantToday($user->id, $merchant->id)) {
            return $this->errorResponse('You have already scanned this merchant today. Come back tomorrow for more points.', 400);
        }

        $pointsEarned = $this->scanMerchantService->createScan($merchant, $user, $request);
        $newBalance = $this->scanOfferService->getUserPointsBalance($user->id);

        $message = $pointsEarned > 0
            ? "Successfully scanned! You earned {$pointsEarned} points from {$merchant->name}."
            : 'Scan recorded.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'merchant' => [
                    'id' => $merchant->id,
                    'name' => $merchant->name,
                ],
                'points_earned' => $pointsEarned,
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
