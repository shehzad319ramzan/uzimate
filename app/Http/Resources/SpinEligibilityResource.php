<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpinEligibilityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'eligible' => $this->resource['eligible'] ?? false,
            'message' => $this->resource['message'] ?? '',
            'next_spin_at' => $this->resource['next_spin_at'] ?? null,
            'spins_used' => $this->resource['spins_used'] ?? 0,
            'spins_per_day' => $this->resource['spins_per_day'] ?? 1,
            'max_points' => $this->resource['max_points'] ?? (int) (config('spin.points_range')[1] ?? 100),
        ];
    }
}
