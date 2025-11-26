<?php

namespace App\Dto;

class OfferDto
{
    public readonly ?string $merchant_id;
    public readonly string $site_id;
    public readonly string $title;
    public readonly int $points_required;
    public readonly ?string $expires_on;
    public readonly ?array $weekdays;
    public readonly ?string $description;
    public readonly ?string $status;
    public $file;

    /**
     * Create a new DTO instance.
     *
     * @return $reauest, $modal
     */
    public function __construct($request)
    {
        $this->merchant_id = $request['merchant_id'] ?? null;
        $this->site_id = $request['site_id'];
        $this->title = $request['title'];
        $this->points_required = (int) $request['points_required'];
        $this->expires_on = $request['expires_on'] ?? null;
        // Handle weekdays - can be array or JSON string
        if (isset($request['weekdays'])) {
            if (is_array($request['weekdays'])) {
                $this->weekdays = $request['weekdays'];
            } elseif (is_string($request['weekdays'])) {
                $decoded = json_decode($request['weekdays'], true);
                $this->weekdays = is_array($decoded) ? $decoded : null;
            } else {
                $this->weekdays = null;
            }
        } else {
            $this->weekdays = null;
        }
        $this->description = $request['description'] ?? null;
        $this->status = $request['status'] ?? '1';
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
        $data = [
            'merchant_id' => $this->merchant_id,
            'site_id' => $this->site_id,
            'title' => $this->title,
            'points_required' => $this->points_required,
            'expires_on' => $this->expires_on,
            'weekdays' => $this->weekdays,
            'description' => $this->description,
            'status' => $this->status,
        ];

        if ($this->file !== null) {
            $data['image'] = $this->file;
        }

        return $data;
    }
}
