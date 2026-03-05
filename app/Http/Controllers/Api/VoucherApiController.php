<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\VoucherResource;
use App\Models\CustomerLog;
use App\Models\Voucher;
use App\Services\ScanOfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherApiController extends ApiBaseController
{
    public function __construct(
        protected ScanOfferService $scanOfferService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $vouchers = $this->getVouchersQuery($request)
            ->orderBy('valid_until')
            ->paginate(min((int) $request->get('per_page', 20), 50));

        return response()->json([
            'success' => true,
            'data' => [
                'vouchers' => VoucherResource::collection($vouchers->getCollection())->resolve(),
                'current_page' => $vouchers->currentPage(),
                'last_page' => $vouchers->lastPage(),
                'per_page' => $vouchers->perPage(),
                'total' => $vouchers->total(),
            ],
        ], 200);
    }



    public function search(Request $request): JsonResponse
    {
        $vouchers = $this->getSearchOnlyQuery($request)
            ->orderBy('valid_until')
            ->paginate(min((int) $request->get('per_page', 20), 50));

        return response()->json([
            'success' => true,
            'data' => [
                'vouchers' => VoucherResource::collection($vouchers->getCollection())->resolve(),
                'current_page' => $vouchers->currentPage(),
                'last_page' => $vouchers->lastPage(),
                'per_page' => $vouchers->perPage(),
                'total' => $vouchers->total(),
            ],
        ], 200);
    }

 
    protected function getSearchOnlyQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $search = $request->query('search') ?? $request->query('q');
        $status = $request->query('status', 'all');

        $query = Voucher::with(['merchant', 'offers']);

        if ($status === 'active') {
            $query->where('status', '1')->notExpired();
        } elseif ($status === 'expired') {
            $query->where('status', '1')
                ->whereNotNull('valid_until')
                ->where('valid_until', '<', now()->toDateString());
        } elseif ($status === 'inactive') {
            $query->where('status', '0');
        }

        if ($search && strlen(trim($search)) > 0) {
            $term = '%' . trim($search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhereHas('merchant', fn ($qb) => $qb->where('name', 'like', $term))
                    ->orWhereHas('offers', fn ($qb) => $qb->where('title', 'like', $term));
            });
        }

        return $query;
    }

    /**
     * Build vouchers query with optional search, status, and merchant filters.
     */
    protected function getVouchersQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $merchantId = $request->query('merchant_id');
        $status = $request->query('status', 'all');
        $search = $request->query('search') ?? $request->query('q');

        $query = Voucher::with(['merchant', 'offers']);

        if ($status === 'active') {
            $query->where('status', '1')->notExpired();
        } elseif ($status === 'expired') {
            $query->where('status', '1')
                ->whereNotNull('valid_until')
                ->where('valid_until', '<', now()->toDateString());
        } elseif ($status === 'inactive') {
            $query->where('status', '0');
        }

        if ($search && strlen(trim($search)) > 0) {
            $term = '%' . trim($search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhereHas('merchant', fn ($qb) => $qb->where('name', 'like', $term));
            });
        }

        if ($merchantId) {
            $query->where('merchant_id', $merchantId);
        }

        return $query;
    }


    public function show(string $id): JsonResponse
    {
        $voucher = Voucher::with(['merchant', 'offers'])->where('status', '1')->find($id);
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Voucher not found.'], 404);
        }
        $data = (new VoucherResource($voucher))->resolve();
        if (Auth::check()) {
            $user = Auth::user();
            $merchant = $voucher->merchant;
            $data['points_available_for_this_voucher'] = $merchant->use_other_merchant_points
                ? $this->scanOfferService->getUserPointsBalance($user->id)
                : $this->scanOfferService->getUserPointsBalanceExcludingActionTypes($user->id, ['qr_code_scanned', 'survey_completed']);
            $data['points_usage_note'] = $merchant->use_other_merchant_points
                ? 'You can use your total points balance for this voucher.'
                : 'Points from offer scans and surveys cannot be used. Your other points (e.g. login, spin) can be used for this voucher.';
        }
        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }

    public function redeem(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'offer_ids' => ['required', 'array', 'min:1'],
            'offer_ids.*' => ['required', 'uuid', 'exists:offers,id'],
        ]);

        $voucher = Voucher::with(['merchant', 'offers'])->where('status', '1')->notExpired()->find($id);
        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher not found or no longer valid.',
            ], 404);
        }

        $user = Auth::user();
        $merchant = $voucher->merchant;
        $voucherOfferIds = $voucher->offers->pluck('id')->toArray();
        $selectedOfferIds = array_values(array_unique((array) $request->input('offer_ids')));

        if (empty($voucherOfferIds)) {
            return response()->json([
                'success' => false,
                'message' => 'This voucher has no linked offers. Redemption is not available.',
            ], 422);
        }

        $invalid = array_diff($selectedOfferIds, $voucherOfferIds);
        if (!empty($invalid)) {
            return response()->json([
                'success' => false,
                'message' => 'One or more selected offers are not linked to this voucher.',
            ], 422);
        }

        $pointsRequired = (int) $voucher->offers->whereIn('id', $selectedOfferIds)->sum('points_required');
        if ($pointsRequired <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Selected offers have no points. Please select at least one offer with points.',
            ], 422);
        }

        $totalBalance = $this->scanOfferService->getUserPointsBalance($user->id);

        if ($totalBalance < $pointsRequired) {
            $shortfall = $pointsRequired - $totalBalance;
            return response()->json([
                'success' => false,
                'message' => "Need {$pointsRequired} points. You have {$totalBalance}. Earn {$shortfall} more.",
                'points_required' => $pointsRequired,
                'points_available' => $totalBalance,
                'points_balance_total' => $totalBalance,
            ], 403);
        }

        $alreadyRedeemed = CustomerLog::where('user_id', $user->id)
            ->where('action_type', 'voucher_redeemed')
            ->where('related_model_type', Voucher::class)
            ->where('related_model_id', $voucher->id)
            ->exists();
        if ($alreadyRedeemed) {
            return response()->json([
                'success' => false,
                'message' => 'You have already redeemed this voucher.',
            ], 400);
        }

        $balanceAfter = $totalBalance - $pointsRequired;
        $metadata = ['voucher_id' => $voucher->id];
        if (!empty($selectedOfferIds)) {
            $metadata['redeemed_offer_ids'] = $selectedOfferIds;
        }
        CustomerLog::create([
            'merchant_id' => $voucher->merchant_id,
            'site_id' => null,
            'user_id' => $user->id,
            'action_type' => 'voucher_redeemed',
            'action_category' => 'vouchers',
            'description' => empty($selectedOfferIds)
                ? "Redeemed voucher: {$voucher->title} ({$voucher->merchant->name})"
                : "Redeemed voucher: {$voucher->title} ({$voucher->merchant->name}) – selected offers",
            'points_affected' => -$pointsRequired,
            'points_balance_before' => $totalBalance,
            'points_balance_after' => $balanceAfter,
            'related_model_type' => Voucher::class,
            'related_model_id' => $voucher->id,
            'performed_by_id' => $user->id,
            'metadata' => $metadata,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $newTotalBalance = $this->scanOfferService->getUserPointsBalance($user->id);
        $voucherData = (new VoucherResource($voucher))->resolve();
        $voucherData['points_balance_after_redeem'] = $newTotalBalance;
        $voucherData['points_deducted'] = $pointsRequired;
        if (!empty($selectedOfferIds)) {
            $voucherData['redeemed_offers'] = $voucher->offers->whereIn('id', $selectedOfferIds)->map(fn ($o) => [
                'id' => $o->id,
                'title' => $o->title,
                'points' => (int) $o->points_required,
            ])->values()->all();
        }

        $message = !empty($selectedOfferIds)
            ? 'Voucher redeemed successfully! ' . $pointsRequired . ' points have been deducted for your selected offers. You can view your receipt in your account.'
            : 'Voucher redeemed successfully! ' . $pointsRequired . ' points have been deducted. You can view your receipt and voucher details in your account.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $voucherData,
        ], 200);
    }
}
