<?php

namespace App\Dto;

class VoucherDto
{
    public ?string $merchant_id;
    /** @var array<int, string>|null */
    public ?array $offer_ids;
    public ?string $title;
    public ?string $description;
    public ?string $terms_and_conditions;
    public ?int $points_required;
    public ?string $valid_until;
    public ?string $status;
    public $file;

    public function __construct($request)
    {
        $this->merchant_id = !empty($request['merchant_id']) ? $request['merchant_id'] : null;
        $raw = $request['offer_ids'] ?? $request['offer_id'] ?? [];
        $this->offer_ids = is_array($raw) ? array_values(array_filter(array_map('strval', $raw))) : ($raw ? [ (string) $raw ] : null);
        $this->title = $request['title'] ?? null;
        $this->description = $request['description'] ?? null;
        $this->terms_and_conditions = $request['terms_and_conditions'] ?? null;
        $this->points_required = isset($request['points_required']) ? (int) $request['points_required'] : 0;
        $this->valid_until = $request['valid_until'] ?? null;
        $this->status = $request['status'] ?? '1';
        $this->file = $request['file'] ?? request()->file('file') ?? null;
    }

    public static function fromRequest($request): self
    {
        $data = is_array($request) ? $request : $request->all();
        return new self($data);
    }

    public function toArray(): array
    {
        return [
            'merchant_id' => $this->merchant_id,
            'title' => $this->title,
            'description' => $this->description,
            'terms_and_conditions' => $this->terms_and_conditions,
            'points_required' => $this->points_required,
            'valid_until' => $this->valid_until,
            'status' => $this->status,
        ];
    }
}
