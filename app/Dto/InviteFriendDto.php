<?php

namespace App\Dto;

class InviteFriendDto
{
    public ?string $referrer_id;
    public ?string $referred_user_id;
    public ?int $points_awarded;

    public function __construct($data)
    {
        $this->referrer_id = $data['referrer_id'] ?? null;
        $this->referred_user_id = $data['referred_user_id'] ?? null;
        $this->points_awarded = isset($data['points_awarded']) ? (int) $data['points_awarded'] : 0;
    }

    public static function fromRequest($data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return [
            'referrer_id' => $this->referrer_id,
            'referred_user_id' => $this->referred_user_id,
            'points_awarded' => $this->points_awarded,
        ];
    }
}
