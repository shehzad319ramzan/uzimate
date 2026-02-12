<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'logo' => $this->logo(),
            'max_sites' => $this->max_sites,
            'spin_after_days' => $this->spin_after_days,
            'scan_after_hours' => $this->scan_after_hours,
            'status' => $this->status,
            'category' => $this->whenLoaded('category', function () {
                return $this->category ? [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ] : null;
            }),
            'category_name' => $this->category?->name ?? 'Not Assign',
        ];
    }
}
