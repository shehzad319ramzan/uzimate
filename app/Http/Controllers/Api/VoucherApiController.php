<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\VoucherResource;
use App\Models\CustomerLog;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherApiController extends ApiBaseController
{

    public function index(Request $request): JsonResponse
    {
        $merchantId = $request->query('merchant_id');
        $status = $request->query('status', 'active');
        $search = $request->query('search');

        $query = Voucher::with(['merchant', 'offers'])->active();

        if ($status === 'active') {
            $query->notExpired();
        } elseif ($status === 'expired') {
            $query->whereNotNull('valid_until')->where('valid_until', '<', now()->toDateString());
        } elseif ($status === 'inactive') {
            $query->where('status', '0');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('merchant', fn ($qb) => $qb->where('name', 'like', "%{$search}%"));
            });
        }

        if ($merchantId) {
            $query->where(function ($q) use ($merchantId) {
                $q->where('merchant_id', $merchantId)
                    ->orWhereHas('merchant', fn ($qb) => $qb->where('use_other_merchant_points', true));
            });
        } else {
            $query->whereHas('merchant', fn ($qb) => $qb->where('use_other_merchant_points', true));
        }

        $vouchers = $query->orderBy('valid_until')->paginate(min((int) $request->get('per_page', 20), 50));

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


    public function show(string $id): JsonResponse
    {
        $voucher = Voucher::with(['merchant', 'offers'])->where('status', '1')->find($id);
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Voucher not found.'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => (new VoucherResource($voucher))->resolve(),
        ], 200);
    }

    public function redeem(Request $request, string $id): JsonResponse
    {
        $request->validate(['merchant_id' => ['nullable', 'uuid', 'exists:merchants,id']]);

        $voucher = Voucher::with(['merchant', 'offers'])->where('status', '1')->notExpired()->find($id);
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Voucher not found or no longer valid.'], 404);
        }

        $user = Auth::user();
        $merchant = $voucher->merchant;

        if (!$merchant->use_other_merchant_points) {
            $contextMerchantId = $request->input('merchant_id');
            if (!$contextMerchantId || $contextMerchantId !== $voucher->merchant_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'This voucher can only be used with points from ' . $merchant->name . '.',
                ], 403);
            }
        }

        $alreadyRedeemed = CustomerLog::where('user_id', $user->id)
            ->where('action_type', 'voucher_redeemed')
            ->where('related_model_type', Voucher::class)
            ->where('related_model_id', $voucher->id)
            ->exists();
        if ($alreadyRedeemed) {
            return response()->json(['success' => false, 'message' => 'You have already redeemed this voucher.'], 400);
        }

        CustomerLog::create([
            'merchant_id' => $voucher->merchant_id,
            'site_id' => null,
            'user_id' => $user->id,
            'action_type' => 'voucher_redeemed',
            'action_category' => 'vouchers',
            'description' => "Redeemed voucher: {$voucher->title} ({$voucher->merchant->name})",
            'points_affected' => - (int) $voucher->points_required,
            'related_model_type' => Voucher::class,
            'related_model_id' => $voucher->id,
            'performed_by_id' => $user->id,
            'metadata' => ['voucher_id' => $voucher->id],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your voucher has been redeemed successfully! Please click on \'View Receipt\' to see your receipt and voucher details.',
            'data' => (new VoucherResource($voucher))->resolve(),
        ], 200);
    }
}
