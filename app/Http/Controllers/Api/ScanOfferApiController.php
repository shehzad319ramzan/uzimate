<?php

namespace App\Http\Controllers\Api;

use App\Models\Merchant;
use App\Models\Offer;
use App\Models\OfferScan;
use App\Services\ScanMerchantService;
use App\Services\ScanOfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanOfferApiController extends ApiBaseController
{
    public function __construct(
        protected ScanOfferService $scanOfferService,
        protected ScanMerchantService $scanMerchantService
    ) {}

    /**
     * List current user's offer scans (scan history from offer_scans table).
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $perPage = min((int) $request->get('per_page', 20), 50);

        $scans = OfferScan::where('user_id', $user->id)
            ->with(['offer:id,title,points_required', 'merchant:id,name'])
            ->latest()
            ->paginate($perPage);

        $items = $scans->getCollection()->map(function (OfferScan $scan) {
            return [
                'id' => $scan->id,
                'offer_id' => $scan->offer_id,
                'offer_title' => $scan->offer?->title ?? ($scan->metadata['offer_title'] ?? null),
                'points_earned' => $scan->points_earned,
                'scanned_at' => $scan->created_at->toIso8601String(),
                'merchant_name' => $scan->merchant?->name,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'scans' => $items,
                'current_page' => $scans->currentPage(),
                'last_page' => $scans->lastPage(),
                'per_page' => $scans->perPage(),
                'total' => $scans->total(),
            ],
        ], 200);
    }


    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'id' => ['required', 'string'],
        ]);

        $user = Auth::user();
        $id = $request->input('id');

        $offer = Offer::find($id);
        if ($offer) {
            return $this->scanOffer($offer, $user, $request);
        }

        $merchant = Merchant::find($id);
        if ($merchant) {
            return $this->scanMerchant($merchant, $user, $request);
        }

        return $this->errorResponse('Invalid scan code. Not an offer or merchant.', 404);
    }

    private function scanOffer($offer, $user, Request $request): JsonResponse
    {
        $offer = $this->scanOfferService->findValidOffer($offer->id);
        if (! $offer) {
            return $this->errorResponse('Invalid or inactive offer', 404);
        }

        [$valid, $message] = $this->scanOfferService->validateExpiry($offer);
        if (! $valid) {
            return $this->errorResponse($message, 400);
        }

        [$valid, $message] = $this->scanOfferService->validateWeekdays($offer);
        if (! $valid) {
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
                'type' => 'offer',
                'offer' => [
                    'id' => $offer->id,
                    'title' => $offer->title,
                    'points_earned' => $pointsEarned,
                ],
                'points_earned' => $pointsEarned,
                'points_balance' => $newBalance,
            ],
        ], 200);
    }

    private function scanMerchant($merchant, $user, Request $request): JsonResponse
    {
        $merchant = $this->scanMerchantService->findMerchantById($merchant->id);
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
                'type' => 'merchant',
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
