<?php

namespace App\Dto;

class MerchantCategoryDto
{
    public ?string $name;
    public ?string $status;

    public function __construct($request)
    {
        $data = is_array($request) ? $request : $request->all();
        $this->name = $data['name'] ?? null;
        $this->status = $data['status'] ?? '1';
    }

    public static function fromRequest($request)
    {
        return new self($request);
    }

    public function toArray(): array
    {
        $data = [];
        if ($this->name !== null) {
            $data['name'] = $this->name;
        }
        if ($this->status !== null) {
            $data['status'] = $this->status;
        }
        return $data;
    }
}
