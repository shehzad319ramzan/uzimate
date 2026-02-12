<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $address = collect([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->county,
            $this->postcode,
            $this->country,
        ])->filter()->implode(', ');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $address ?: null,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'city' => $this->city,
            'county' => $this->county,
            'postcode' => $this->postcode,
            'country' => $this->country,
            'phone' => $this->phone,
            'coordinates' => $this->coordinates,
            'logo' => $this->displayLogo(),
            'info' => $this->description,
            'points' => $this->points,
            'operating_hours' => null,
        ];
    }
}
