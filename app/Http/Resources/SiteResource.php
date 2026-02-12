<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'points' => $this->points,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'city' => $this->city,
            'county' => $this->county,
            'postcode' => $this->postcode,
            'country' => $this->country,
            'location' => $this->location,
            'coordinates' => $this->coordinates,
            'logo' => $this->displayLogo(),
            'use_merchant_logo' => (bool) ($this->use_merchant_logo ?? false),
            'info' => $this->description,
            'description' => $this->description,
            'status' => $this->status,
        ];
    }
}
