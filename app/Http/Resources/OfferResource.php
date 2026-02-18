<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $arr = [
            'id' => $this->id,
            'title' => $this->title,
            'points_required' => $this->points_required,
            'points' => $this->points_required,
            'description' => $this->description,
            'expires_on' => $this->expires_on?->format('Y-m-d'),
            'image' => $this->image(),
            'status' => $this->status,
            'merchant_name' => $this->merchant?->name ?? null,
            'merchant_logo' => $this->merchant?->logo() ?? null,
            'merchant_id' => $this->merchant_id,
            'qr_code_image' => $this->resource->qrCodeImageUrl(),
        ];

        if (isset($this->user_scan)) {
            $arr['user_has_scanned'] = true;
            $arr['scanned_at'] = $this->user_scan->created_at?->toIso8601String();
            $arr['points_earned'] = $this->user_scan->points_earned ?? null;
        }

        return $arr;
    }
}
