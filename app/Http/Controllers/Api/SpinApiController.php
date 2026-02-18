<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SpinEligibilityResource;
use App\Http\Resources\SpinHistoryResource;
use App\Services\SpinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpinApiController extends ApiBaseController
{
    public function __construct(protected SpinService $spinService)
    {
    }

    /**
     * GET /api/spin/eligibility – Check if user can spin today.
     */
    public function eligibility(Request $request): JsonResponse
    {
        $user = Auth::user();
        $siteId = $request->query('site_id');

        $result = $this->spinService->getEligibility($user->id, $siteId ?: null);

        return response()->json([
            'success' => true,
            'data' => (new SpinEligibilityResource($result))->toArray($request),
        ], 200);
    }

    public function spin(Request $request): JsonResponse
    {
        $request->validate([
            'site_id' => ['nullable', 'string', 'uuid', 'exists:sites,id'],
        ]);

        $user = Auth::user();
        $siteId = $request->input('site_id');

        try {
            $spinHistory = $this->spinService->performSpin($user->id, $request, $siteId);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }

        $message = $this->buildResultMessage($spinHistory);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'spin' => (new SpinHistoryResource($spinHistory))->toArray($request),
            ],
        ], 200);
    }

    protected function buildResultMessage($spinHistory): string
    {
        return match ($spinHistory->spin_result_type) {
            'points' => "You won {$spinHistory->points_earned} points!",
            'offer' => $spinHistory->offer
                ? "You won an offer: {$spinHistory->offer->title}!"
                : 'You won an offer!',
            'discount' => $spinHistory->reward_value
                ? 'You won ' . number_format((float) $spinHistory->reward_value, 0) . '% discount!'
                : 'You won a discount!',
            default => 'Better luck next time!',
        };
    }

    protected function errorResponse(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
