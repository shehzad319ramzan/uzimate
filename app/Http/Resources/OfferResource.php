<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
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
    }
}
