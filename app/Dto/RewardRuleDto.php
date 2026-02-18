<?php

namespace App\Dto;

class RewardRuleDto
{
    public readonly ?string $merchant_id;
    public readonly string $action_type;
    public readonly string $label;
    public readonly ?int $points;
    public readonly string $trigger_condition;
    public readonly bool $is_active;

    public function __construct(array $data)
    {
        $this->merchant_id = $data['merchant_id'] ?? null;
        $this->action_type = $data['action_type'];
        $this->label = $data['label'];
        $this->points = isset($data['points']) ? (int) $data['points'] : null;
        $this->trigger_condition = $data['trigger_condition'] ?? 'every_time';
        $this->is_active = (bool) ($data['is_active'] ?? true);
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
            'merchant_id' => $this->merchant_id,
            'action_type' => $this->action_type,
            'label' => $this->label,
            'points' => $this->points,
            'trigger_condition' => $this->trigger_condition,
            'is_active' => $this->is_active,
        ];
    }
}
