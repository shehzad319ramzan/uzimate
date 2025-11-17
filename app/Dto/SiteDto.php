<?php

namespace App\Dto;

class SiteDto
{
    public ?string $merchant_id;
    public ?string $name;
    public ?string $phone;
    public ?int $points;
    public ?string $address_line_1;
    public ?string $address_line_2;
    public ?string $city;
    public ?string $county;
    public ?string $postcode;
    public ?string $country;
    public ?string $location;
    public ?string $coordinates;
    public ?bool $use_merchant_logo;
    public $file;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct($request)
    {
        $this->merchant_id = isset($request['merchant_id']) ? $request['merchant_id'] : null;
        $this->name = isset($request['name']) ? $request['name'] : null;
        $this->phone = isset($request['phone']) ? $request['phone'] : null;
        $this->points = isset($request['points']) ? (int)$request['points'] : null;
        $this->address_line_1 = isset($request['address_line_1']) ? $request['address_line_1'] : null;
        $this->address_line_2 = isset($request['address_line_2']) ? $request['address_line_2'] : null;
        $this->city = isset($request['city']) ? $request['city'] : null;
        $this->county = isset($request['county']) ? $request['county'] : null;
        $this->postcode = isset($request['postcode']) ? $request['postcode'] : null;
        $this->country = isset($request['country']) ? $request['country'] : 'United Kingdom';
        $this->location = isset($request['location']) ? $request['location'] : null;
        $this->coordinates = isset($request['coordinates']) ? $request['coordinates'] : null;
        $this->use_merchant_logo = isset($request['use_merchant_logo']) ? (bool)$request['use_merchant_logo'] : false;
        $this->file = request()->hasFile('file') ? request()->file('file') : null;
    }

    public static function fromRequest($request)
    {
        if (is_array($request)) {
            return new self($request);
        }
        return new self($request->all());
    }

    public function toArray()
    {
        $data = [];
        
        if ($this->merchant_id !== null) {
            $data['merchant_id'] = $this->merchant_id;
        }
        
        if ($this->name !== null) {
            $data['name'] = $this->name;
        }
        
        if ($this->phone !== null) {
            $data['phone'] = $this->phone;
        }
        
        if ($this->points !== null) {
            $data['points'] = $this->points;
        }
        
        if ($this->address_line_1 !== null) {
            $data['address_line_1'] = $this->address_line_1;
        }
        
        if ($this->address_line_2 !== null) {
            $data['address_line_2'] = $this->address_line_2;
        }
        
        if ($this->city !== null) {
            $data['city'] = $this->city;
        }
        
        if ($this->county !== null) {
            $data['county'] = $this->county;
        }
        
        if ($this->postcode !== null) {
            $data['postcode'] = $this->postcode;
        }
        
        if ($this->country !== null) {
            $data['country'] = $this->country;
        }
        
        if ($this->location !== null) {
            $data['location'] = $this->location;
        }
        
        if ($this->coordinates !== null) {
            $data['coordinates'] = $this->coordinates;
        }
        
        $data['use_merchant_logo'] = $this->use_merchant_logo ?? false;
        
        if ($this->file !== null) {
            $data['image'] = $this->file;
        }

        return $data;
    }
}
