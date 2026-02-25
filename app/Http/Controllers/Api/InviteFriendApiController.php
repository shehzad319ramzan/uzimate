<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\InvitedFriendResource;
use App\Http\Resources\ReferralCodeResource;
use App\Models\User;
use App\Services\RewardRuleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class InviteFriendApiController extends ApiBaseController
{
    public function __construct(protected RewardRuleService $rewardRuleService)
    {
    }

    /**
     * GET /api/referral-code – Current user's referral code and points per referral.
     */
    public function referralCode(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $code = $user->referral_code ?? null;
        if (!$code) {
            $user->referral_code = User::generateUniqueReferralCode();
            $user->save();
            $code = $user->referral_code;
        }
        $pointsPerReferral = (int) ($this->rewardRuleService->getPointsForAction('referral_completed', null) ?? config('referral.points_per_referral', 5000));

        return response()->json([
            'success' => true,
            'data' => (new ReferralCodeResource([
                'referral_code' => $code,
                'points_per_referral' => $pointsPerReferral,
            ]))->resolve(),
        ], 200);
    }

    /**
     * GET /api/invited-friends – List of users who joined using current user's referral code.
     */
    public function invitedFriends(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $list = $user->referredFriends()->with('referredUser')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => [
                'invited_friends' => InvitedFriendResource::collection($list)->resolve(),
            ],
        ], 200);
    }
}
