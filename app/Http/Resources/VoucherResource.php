<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->resolveStatus(),
            'title' => $this->title,
            'description' => $this->description,
            'terms_and_conditions' => $this->terms_and_conditions,
            'points_required' => (int) $this->points_required,
            'valid_until' => $this->valid_until?->format('Y-m-d'),
            'valid_until_formatted' => $this->valid_until?->format('M d, Y'),
            'merchant' => $this->whenLoaded('merchant', function () {
                return [
                    'id' => $this->merchant->id,
                    'name' => $this->merchant->name,
                    'use_other_merchant_points' => (bool) $this->merchant->use_other_merchant_points,
                ];
            }),
            'offers' => $this->whenLoaded('offers', function () {
                return $this->offers->map(fn ($o) => [
                    'id' => $o->id,
                    'title' => $o->title,
                    'points' => (int) ($o->points_required ?? 0),
                    'image_url' => $o->image() ?: null,
                ]);
            }),
            'image_url' => $this->image() ?: null,
        ];
    }

    /**
     * Resolve voucher status: active, inactive, or expired.
     */
    protected function resolveStatus(): string
    {
        if ((string) $this->status === '0') {
            return 'inactive';
        }
        if ($this->valid_until && $this->valid_until->isPast()) {
            return 'expired';
        }
        return 'active';
    }
}
