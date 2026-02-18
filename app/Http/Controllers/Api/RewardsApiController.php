<?php

namespace App\Http\Controllers\Api;

use App\Models\Offer;
use App\Services\RewardRuleService;
use App\Services\ScanOfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class RewardsApiController extends ApiBaseController
{
    public function __construct(
        protected ScanOfferService $scanOfferService,
        protected RewardRuleService $rewardRuleService
    ) {}

    /**
     * My Rewards - points balance + reward history.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $perPage = min((int) $request->get('per_page', 20), 50);

        $pointsBalance = $this->scanOfferService->getUserPointsBalance($user->id);
        $history = $this->scanOfferService->getRewardHistory($user->id, $perPage);

        $historyItems = $history->getCollection()->map(function ($log) {
            return [
                'id' => $log->id,
                'description' => $log->description,
                // 'action_type' => $log->action_type,
                'action_type_label' => $this->rewardRuleService->getActionTypeLabel($log->action_type, $log->merchant_id),
                'points' => $log->points_affected,
                'date' => $log->created_at->format('d M Y'),
                'offer_image' => $this->getOfferImage($log) ?? $this->getDefaultRewardImage(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'points_balance' => $pointsBalance,
                'reward_history' => [
                    'data' => $historyItems,
                    'current_page' => $history->currentPage(),
                    'last_page' => $history->lastPage(),
                    'per_page' => $history->perPage(),
                    'total' => $history->total(),
                ],
            ],
        ], 200);
    }

    protected function getOfferImage($log): ?string
    {
        $offer = $log->offer ?? $log->relatedModel;
        if ($offer instanceof Offer) {
            return $offer->image();
        }
        return null;
    }

    protected function getDefaultRewardImage(): string
    {
        return URL::asset('dashboard/images/default/user_logo.png');
    }
}
