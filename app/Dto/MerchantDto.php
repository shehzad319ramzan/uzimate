<?php

namespace App\Dto;

class MerchantDto
{
    public ?string $name;
    public ?int $max_sites;
    public ?int $spin_after_days;
    public ?int $scan_after_hours;
    public ?bool $use_other_merchant_points;
    public $file;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct($request)
    {
        $this->name = isset($request['merchant_name']) ? $request['merchant_name'] : null;
        $this->max_sites = isset($request['max_sites']) ? (int)$request['max_sites'] : null;
        $this->spin_after_days = isset($request['spin_after_days']) ? (int)$request['spin_after_days'] : null;
        $this->scan_after_hours = isset($request['scan_after_hours']) ? (int)$request['scan_after_hours'] : null;
        $this->use_other_merchant_points = isset($request['use_other_merchant_points']) ? (bool)$request['use_other_merchant_points'] : false;
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
        
        if ($this->name !== null) {
            $data['name'] = $this->name;
        }
        
        if ($this->max_sites !== null) {
            $data['max_sites'] = $this->max_sites;
        }
        
        if ($this->spin_after_days !== null) {
            $data['spin_after_days'] = $this->spin_after_days;
        }
        
        if ($this->scan_after_hours !== null) {
            $data['scan_after_hours'] = $this->scan_after_hours;
        }
        
        $data['use_other_merchant_points'] = $this->use_other_merchant_points ?? false;
        
        if ($this->file !== null) {
            $data['image'] = $this->file;
        }

        return $data;
    }
}
