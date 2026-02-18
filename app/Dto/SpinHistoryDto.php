<?php

namespace App\Dto;

class SpinHistoryDto
{
    public readonly ?string $site_id;
    public readonly int $user_id;
    public readonly string $spin_result_type;
    public readonly int $points_earned;
    public readonly ?string $offer_id;
    public readonly ?float $reward_value;
    public readonly ?int $spin_number;
    public readonly bool $is_eligible;
    public readonly ?string $last_spin_date;
    public readonly ?string $notes;
    public readonly ?string $ip_address;
    public readonly ?array $device_info;

    /**
     * Create a new DTO instance.
     */
    public function __construct(array $data)
    {
        $this->site_id = isset($data['site_id']) ? (string) $data['site_id'] : null;
        $this->user_id = (int) $data['user_id'];
        $this->spin_result_type = $data['spin_result_type'] ?? 'nothing';
        $this->points_earned = isset($data['points_earned']) ? (int) $data['points_earned'] : 0;
        $this->offer_id = $data['offer_id'] ?? null;
        $this->reward_value = isset($data['reward_value']) ? (float) $data['reward_value'] : null;
        $this->spin_number = isset($data['spin_number']) ? (int) $data['spin_number'] : 1;
        $this->is_eligible = isset($data['is_eligible']) ? (bool) $data['is_eligible'] : true;
        $this->last_spin_date = $data['last_spin_date'] ?? null;
        $this->notes = $data['notes'] ?? null;
        $this->ip_address = $data['ip_address'] ?? request()->ip();
        $this->device_info = isset($data['device_info']) ? (is_array($data['device_info']) ? $data['device_info'] : json_decode($data['device_info'], true)) : null;
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
        $data = [
            'user_id' => $this->user_id,
            'spin_result_type' => $this->spin_result_type,
            'points_earned' => $this->points_earned,
            'spin_number' => $this->spin_number,
            'is_eligible' => $this->is_eligible,
            'notes' => $this->notes,
            'ip_address' => $this->ip_address,
            'device_info' => $this->device_info,
        ];

        if ($this->offer_id !== null) {
            $data['offer_id'] = $this->offer_id;
        }

        if ($this->reward_value !== null) {
            $data['reward_value'] = $this->reward_value;
        }

        if ($this->last_spin_date !== null) {
            $data['last_spin_date'] = $this->last_spin_date;
        }

        if ($this->site_id !== null) {
            $data['site_id'] = $this->site_id;
        }

        return $data;
    }
}
