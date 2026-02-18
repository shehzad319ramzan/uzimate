<?php

namespace App\Services;

use App\Dto\SpinHistoryDto;
use App\Models\Offer;
use App\Models\Site;
use App\Models\SpinHistory;
use App\Repositories\SpinHistoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SpinService
{
    public function __construct(protected SpinHistoryRepository $spinHistoryRepository)
    {
    }

    /**
     * Check if user can spin (eligibility: 1 per day by default).
     */
    public function getEligibility(string $userId, ?string $siteId = null): array
    {
        $siteId = $siteId ?: $this->getDefaultSiteId();
        if (!$siteId) {
            return [
                'eligible' => false,
                'message' => 'No spin site configured.',
                'next_spin_at' => null,
            ];
        }

        $spinsPerDay = (int) config('spin.spins_per_day', 1);
        $today = Carbon::today()->toDateString();

        $spinsToday = SpinHistory::where('user_id', $userId)
            ->where('site_id', $siteId)
            ->whereDate('created_at', $today)
            ->count();

        if ($spinsToday >= $spinsPerDay) {
            $nextSpinAt = Carbon::today()->addDay()->startOfDay();
            return [
                'eligible' => false,
                'message' => 'You have used your spin for today. Come back tomorrow!',
                'next_spin_at' => $nextSpinAt->toIso8601String(),
                'spins_used' => $spinsToday,
                'spins_per_day' => $spinsPerDay,
            ];
        }

        return [
            'eligible' => true,
            'message' => 'You can spin.',
            'next_spin_at' => null,
            'spins_used' => $spinsToday,
            'spins_per_day' => $spinsPerDay,
        ];
    }

    /**
     * Perform spin: check eligibility, determine result, store via SpinHistoryRepository (observer creates customer log).
     */
    public function performSpin(string $userId, Request $request, ?string $siteId = null): SpinHistory
    {
        $siteId = $siteId ?: $this->getDefaultSiteId();
        if (!$siteId) {
            throw new \InvalidArgumentException('No spin site configured.');
        }

        $eligibility = $this->getEligibility($userId, $siteId);
        if (!$eligibility['eligible']) {
            throw new \RuntimeException($eligibility['message']);
        }

        $result = $this->determineResult($siteId);

        $payload = [
            'site_id' => $siteId,
            'user_id' => (int) $userId,
            'spin_result_type' => $result['type'],
            'points_earned' => $result['points'] ?? 0,
            'offer_id' => $result['offer_id'] ?? null,
            'reward_value' => $result['reward_value'] ?? null,
            'spin_number' => 0, // let repository calculate next spin number
            'is_eligible' => true,
            'last_spin_date' => Carbon::today()->toDateString(),
            'notes' => null,
            'ip_address' => $request->ip(),
            'device_info' => $request->userAgent() ? ['user_agent' => $request->userAgent()] : null,
        ];

        $dto = new SpinHistoryDto($payload);
        $spinHistory = $this->spinHistoryRepository->store($dto);

        return $spinHistory->load(['offer', 'site', 'merchant']);
    }

    /**
     * Weighted random outcome: nothing, points, offer, discount.
     */
    protected function determineResult(string $siteId): array
    {
        $outcomes = config('spin.outcomes', [
            'nothing' => 50,
            'points' => 30,
            'offer' => 15,
            'discount' => 5,
        ]);
        $rand = random_int(1, 100);
        $cumulative = 0;
        $type = 'nothing';

        foreach ($outcomes as $outcomeType => $percent) {
            $cumulative += $percent;
            if ($rand <= $cumulative) {
                $type = $outcomeType;
                break;
            }
        }

        $result = ['type' => $type, 'points' => 0, 'offer_id' => null, 'reward_value' => null];

        if ($type === 'points') {
            $range = config('spin.points_range', [25, 100]);
            $result['points'] = random_int($range[0], $range[1]);
        } elseif ($type === 'offer') {
            $offer = $this->pickRandomOfferForSite($siteId);
            $result['offer_id'] = $offer?->id;
        } elseif ($type === 'discount') {
            $range = config('spin.discount_range', [5, 20]);
            $result['reward_value'] = (float) random_int($range[0], $range[1]);
        }

        return $result;
    }

    protected function pickRandomOfferForSite(string $siteId): ?Offer
    {
        return Offer::where('site_id', $siteId)
            ->where(function ($q) {
                $q->where('status', '1')->orWhere('status', 1);
            })
            ->where(function ($q) {
                $q->whereNull('expires_on')->orWhere('expires_on', '>=', now());
            })
            ->inRandomOrder()
            ->first();
    }

    public function getDefaultSiteId(): ?string
    {
        $id = config('spin.default_site_id');
        if ($id) {
            return Site::where('id', $id)->exists() ? $id : null;
        }
        $first = Site::select('id')->first();
        return $first?->id;
    }
}
