<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\OfferResource;
use App\Models\Offer;
use App\Models\OfferScan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferApiController extends ApiBaseController
{
    /**
     * List all offers (Promo & Rewards).
     * Supports filtering by merchant, search, and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Offer::with('merchant')
            ->where(function ($q) {
                $q->where('status', '1')->orWhere('status', 1);
            })
            ->whereNotNull('merchant_id')
            ->orderBy('created_at', 'desc');

        if ($request->filled('merchant_id')) {
            $query->where('merchant_id', $request->merchant_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('merchant', fn ($m) => $m->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('min_points')) {
            $query->where('points_required', '>=', (int) $request->min_points);
        }

        if ($request->filled('max_points')) {
            $query->where('points_required', '<=', (int) $request->max_points);
        }

        $perPage = min((int) $request->get('per_page', 20), 100);
        $offers = $query->paginate($perPage);

        if (Auth::check()) {
            $scans = OfferScan::where('user_id', Auth::id())
                ->whereIn('offer_id', $offers->pluck('id'))
                ->get()
                ->keyBy('offer_id');
            $offers->getCollection()->each(function ($offer) use ($scans) {
                $offer->user_scan = $scans->get($offer->id);
            });
        }

        return response()->json([
            'success' => true,
            'data' => OfferResource::collection($offers->getCollection()),
            'meta' => [
                'current_page' => $offers->currentPage(),
                'last_page' => $offers->lastPage(),
                'per_page' => $offers->perPage(),
                'total' => $offers->total(),
            ],
        ], 200);
    }

    /**
     * Get single offer detail.
     */
    public function show(string $id): JsonResponse
    {
        $offer = $this->get_by_id(new Offer(), $id, ['merchant', 'site']);

        if (!$offer || ($offer->status != '1' && $offer->status != 1)) {
            return response()->json([
                'success' => false,
                'message' => 'Offer not found',
            ], 404);
        }

        if (Auth::check()) {
            $offer->user_scan = OfferScan::where('user_id', Auth::id())
                ->where('offer_id', $offer->id)
                ->first();
        }

        return response()->json([
            'success' => true,
            'data' => new OfferResource($offer),
        ], 200);
    }
}
