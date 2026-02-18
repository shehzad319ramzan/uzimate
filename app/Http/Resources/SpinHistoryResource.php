<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpinHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'spin_result_type' => $this->spin_result_type,
            'points_earned' => (int) ($this->points_earned ?? 0),
            'spin_number' => (int) ($this->spin_number ?? 1),
            'offer' => $this->when($this->offer, fn () => [
                'id' => $this->offer->id,
                'title' => $this->offer->title,
            ]),
            'discount_percent' => $this->reward_value !== null ? (float) $this->reward_value : null,
        ];
    }
}
