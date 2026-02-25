<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReferralCodeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'referral_code' => $this->resource['referral_code'] ?? null,
            'points_per_referral' => (int) ($this->resource['points_per_referral'] ?? 0),
        ];
    }
}
