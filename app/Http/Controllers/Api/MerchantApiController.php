<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\MerchantCategoryResource;
use App\Http\Resources\MerchantResource;
use App\Http\Resources\OfferResource;
use App\Http\Resources\StoreResource;
use App\Models\Merchant;
use App\Models\MerchantCategory;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MerchantApiController extends ApiBaseController
{
    public function merchantCategories(): JsonResponse
    {
        $categories = $this->get_by_column_without_pagination(
            new MerchantCategory(),
            ['status' => '1']
        );
        $categories = $categories->sortBy('name')->values();

        return response()->json([
            'success' => true,
            'data' => MerchantCategoryResource::collection($categories),
        ], 200);
    }

    public function merchants(Request $request): JsonResponse
    {
        $query = Merchant::with('category')
            ->orderBy('name');

        if ($request->filled('category_id')) {
            $query->where('merchant_category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $perPage = min((int) $request->get('per_page', 20), 100);
        $merchants = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => MerchantResource::collection($merchants->getCollection()),
            'meta' => [
                'current_page' => $merchants->currentPage(),
                'last_page' => $merchants->lastPage(),
                'per_page' => $merchants->perPage(),
                'total' => $merchants->total(),
            ],
        ], 200);
    }

    public function merchantDetail(string $id): JsonResponse
    {
        $merchant = $this->get_by_id(new Merchant(), $id, ['category', 'sites', 'sites.merchant']);

        if (!$merchant) {
            return response()->json([
                'success' => false,
                'message' => 'Merchant not found',
            ], 404);
        }

        $offers =Offer::where('merchant_id', $merchant->id)
            ->with('merchant')
            ->where(function ($q) {
                $q->where('status', '1')->orWhere('status', 1);
            })
            ->get();

        $info = $merchant->description ?? $merchant->sites->first()?->description ?? '';

        $merchant->loadCount('sites');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $merchant->id,
                'name' => $merchant->name,
                'logo' => $merchant->logo(),
                'max_sites' => $merchant->max_sites,
                'spin_after_days' => $merchant->spin_after_days,
                'scan_after_hours' => $merchant->scan_after_hours,
                'status' => $merchant->status,
                'category' => $merchant->category ? [
                    'id' => $merchant->category->id,
                    'name' => $merchant->category->name,
                ] : null,
                'category_name' => $merchant->category?->name ?? 'Not Assign',
                'info' => $info,
                'rewards' => OfferResource::collection($offers),
                'store' => StoreResource::collection($merchant->sites),
            ],
        ], 200);
    }
}
