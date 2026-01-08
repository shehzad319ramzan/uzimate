<?php

namespace App\Dto;

class PointAwardDto
{
    public readonly string $site_id;
    public readonly int $user_id;
    public readonly int $points_earned;
    public readonly ?string $notes;
    public readonly ?int $awarded_by_id;

    /**
     * Create a new DTO instance.
     */
    public function __construct(array $data)
    {
        $this->site_id = $data['site_id'];
        $this->user_id = (int) $data['user_id'];
        $this->points_earned = (int) $data['points_earned'];
        $this->notes = $data['notes'] ?? null;
        $this->awarded_by_id = auth()->id();
    }

    public static function fromRequest($request): self
    {
        if (is_array($request)) {
            return new self($request);
        }

        return new self($request?->all() ?? []);
    }

    public function toArray(): array
    {
        return [
            'site_id' => $this->site_id,
            'user_id' => $this->user_id,
            'points_earned' => $this->points_earned,
            'notes' => $this->notes,
            'awarded_by_id' => $this->awarded_by_id,
        ];
    }
}
